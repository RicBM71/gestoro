<?php

namespace App\Http\Controllers\Utilidades;

use App\Cuenta;
use App\Deposito;
use Illuminate\Http\Request;
use Digitick\Sepa\GroupHeader;
use App\Http\Controllers\Controller;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\DomBuilder\DomBuilderFactory;
use Digitick\Sepa\TransferFile\CustomerCreditTransferFile;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Digitick\Sepa\TransferInformation\CustomerCreditTransferInformation;


class SepaController extends Controller
{

    public function index(){

        if (!hasSepa())
            abort(403,'No Autorizado SEPA');

        if (request()->wantsJson())
            return [
                'cuentas'  => Cuenta::selCuentas(),
                'depositos' => Deposito::with(['compra','cliente'])->remesada(false)
                                    ->whereHas('compra', function ($q) {
                                        $q->where('fase_id', '=', 4);})
                                    ->get(),
            ];
    }

    public function reload(Request $request){

        if (!hasSepa())
            abort(403,'No Autorizado SEPA');

        $data = $request->validate([
            'cuenta_id'=>'required|integer',
            'fecha'=>'required|date',
            'remesada'=>'required|boolean'
        ]);

        if (request()->wantsJson())
            return [
                'depositos' => Deposito::with(['compra','cliente'])
                                    ->remesada($data['remesada'])
                                    ->whereHas('compra', function ($q) {
                                        $q->where('fase_id', '=', 4);})
                                    ->where('fecha', $data['fecha'])
                                    ->get(),
            ];
    }

    public function transfer(Request $request){

        if (!hasSepa())
            abort(403,'No Autorizado SEPA');

        $data = $request->validate([
            'cuenta_id' => 'required|integer',
            'fecha'     => 'required|date'
        ]);


        $transferencias =  Deposito::with(['compra','cliente'])->remesada(false)
                                        ->whereHas('compra', function ($q) {
                                            $q->where('fase_id', '=', 4);})
                                        ->get();

        if ($transferencias->count()==0)
            abort(404,'No hay transferencias para remesar');

        $remesa = $this->generarTransfer($transferencias,$data['cuenta_id'],$data['fecha']);

        if (request()->wantsJson())
            return [
                'xml'            => $remesa['xml'],
                'importe'        => $remesa['importe'],
                'transferencias' => $remesa['transferencias']
            ];

    }

     /**
     * Vamos a generar el fichero de remesa.
     */
    private function generarTransfer($data, $cuenta_id, $fecha){


        $cuenta = Cuenta::find($cuenta_id);

        //$idPayment = session()->get('empresa')->cif;
        $cif = str_replace('-', '', session()->get('empresa')->cif);
        $idPayment = $cif.date('Ymdhis');


                // Create the initiating information
        $groupHeader = new GroupHeader($idPayment, session()->get('empresa')->razon);
        $idSufijo=$cuenta->sufijo_transf;

        $groupHeader->setInitiatingPartyId($idSufijo);
        //$groupHeader->setInitiatingPartyId($cuenta->sepa);

        $sepaFile = new CustomerCreditTransferFile($groupHeader);

        $imp_total_remesa = $transferencias = 0;
        foreach ($data as $row){

            $tr = [
                'remesada' => true,
                'username' => session()->get('username')
            ];

            // TODO: Update remesadas
            $row->update($tr);

            $transferencias++;

            $imp_total_remesa += $row->importe;

            $transfer = new CustomerCreditTransferInformation(
                $row->importe, // Amount
                $row->iban, //IBAN of creditor
                $row->cliente->razon //Name of Creditor
            );

            $transfer->setBic($row->bic); // Set the BIC explicitly
            $transfer->setRemittanceInformation('COMPRA '.$row->compra->albser);
            $transfer->setEndToEndIdentification(uniqid());

            // Create a PaymentInformation the Transfer belongs to
            $payment = new PaymentInformation(
                $idPayment,
                $cuenta->iban, // IBAN the money is transferred from
                $cuenta->bic,  // BIC
                session()->get('empresa')->razon // Debitor Name
            );

            // poner est para indicar que es nómina
            // SALA: Salario
            // OTHR: Otros
            // SUPP: Pagos a proveedores
            $payment->setCategoryPurposeCode('SUPP');
            // It's possible to add multiple Transfers in one Payment
            $payment->addTransfer($transfer);

            // It's possible to add multiple payments to one SEPA File
            $sepaFile->addPaymentInformation($payment);
        }




        // Attach a dombuilder to the sepaFile to create the XML output
        //$domBuilder = DomBuilderFactory::createDomBuilder($sepaFile);

        // Or if you want to use the format 'pain.001.001.03' instead
       $domBuilder = DomBuilderFactory::createDomBuilder($sepaFile, 'pain.001.001.03');

        $xml = $domBuilder->asXml();



        // $customerCredit = TransferFileFacadeFactory::createCustomerCredit(session()->get('empresa')->cif, session()->get('empresa')->razon);

        // $idPayment = session()->get('empresa')->cif."000";

        // $customerCredit->addPaymentInfo($idPayment, array(
        //     'id'                      => $idPayment,
        //     'debtorName'              => session()->get('empresa')->razon,
        //     'debtorAccountIBAN'       => $cuenta->iban,
        //     'debtorAgentBIC'          => $cuenta->bic,
        // ));

        // $imp_total_remesa = $transferencias = 0;
        // foreach ($data as $row){

        //     $t = [
        //         'enviada' => true,
        //         'username' => session()->get('username')
        //     ];
        //     $row->update($t);

        //     $transferencias++;
        //     // create a payment, it's possible to create multiple payments,
        //     // "firstPayment" is the identifier for the transactions

        //     $imp_total_remesa += $row->importe;

        //         // Add a Single Transaction to the named payment
        //     $customerCredit->addTransfer($idPayment, array(
        //         'amount'                  => $row->importe,
        //         'creditorIban'            => $row->iban_abono,
        //         'creditorBic'             => $row->bic_abono,
        //         'creditorName'            => $row->cliente->razon,
        //         'remittanceInformation'   => $row->concepto
        //     ));

        // }

        // $xml = $customerCredit->asXML();

        return [
            'xml' => $xml,
            'importe' => $imp_total_remesa,
            'transferencias' => $transferencias
        ];

        // \Storage::disk('local')->put('remesa.xml',$xml);

        // \Storage::disk('local')->download('remesa.xml');

    }


}

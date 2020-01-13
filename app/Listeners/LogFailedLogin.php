<?php

namespace App\Listeners;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Failed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogFailedLogin
{
    protected $request;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        activity()
            ->withProperties([
                'username' => $this->request->input('username'),
                'ip' => $this->request->ip()
            ])
            ->log('LogFaildedLogin');
    }
}

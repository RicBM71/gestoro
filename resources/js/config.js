const apiDomain = Laravel.apiDomain;
export const siteName = Laravel.siteName;

export const api = {
    login: apiDomain + '/authenticate',
    dash: apiDomain + '/dash',
	currentUser: apiDomain + '/user',
	updateUserProfile: apiDomain + '/user/profile/update',
	updateUserPassword: apiDomain + '/user/password/update'
};

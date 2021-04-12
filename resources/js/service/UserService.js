
export default class UserService {

	getUsers() {
		return axios.get('/adm/user/list').then(res => res.data.data);
	}

	getGroups() {
		return axios.get('/adm/user/groups').then(res => res.data.data);
	}

	getUser(id) {
		return axios.get('/adm/api/user/'+id).then(res => res.data.data);
	}


}
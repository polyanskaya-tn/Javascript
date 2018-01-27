import qs from 'qs'
import {LOAD_ALL_ARTICLES, LOAD_CATEGORIES, LOAD_USERS,
CHANGE_CATEGORIES, CHANGE_USERS} from '../constants'

export function loadAllArticles(category, user, offset, limit) {

    const obj = { category, user, offset, limit };

    let req = qs.stringify(obj);
	if (req) req = '?'+req;

	return {
		type: LOAD_ALL_ARTICLES,
        payload : {offset, limit},
		callAPI: 'http://localhost:8082/api/events'+req
	}
}

export function loadCategories() {

	return {
		type: LOAD_CATEGORIES,
		callAPI: 'http://localhost:8082/api/category'
	}
}

export function loadUsers() {

	return {
		type: LOAD_USERS,
		callAPI: 'http://localhost:8082/api/user'
	}
}

export function changeCategories(id) {
	return {
		type: CHANGE_CATEGORIES,
		payload : {id}
	}
}

export function changeUsers(id) {
	return {
		type: CHANGE_USERS,
		payload : {id}
	}
}
import {LOAD_USERS, START, SUCCESS, FAIL} from '../constants'

export const user = (users = [], action) => {
	const {type, response} = action;
	
	switch (type) {
		case LOAD_USERS+SUCCESS :  
			return users = response.map(function(elem) {
				return Object.assign({}, elem);
			});
	}
	return users;
}

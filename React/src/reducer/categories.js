import {LOAD_CATEGORIES, START, SUCCESS, FAIL} from '../constants'

export const category = (categories = [], action) => {
	const {type, response} = action;
	
	switch (type) {
		case LOAD_CATEGORIES+SUCCESS :  {
			const [ ...newCategos] = response;
			return newCategos;			
		}
	}
	return categories;
}


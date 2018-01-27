import {LOAD_ALL_ARTICLES, LOAD_CATEGORIES, 
	SUCCESS, EVENTS_OFFSET,EVENTS_LIMIT,
	CHANGE_CATEGORIES, CHANGE_USERS} from '../constants'

function setInitState() {
	return  (
		{
			categoryId: null,
			userId: null,
			offset:EVENTS_OFFSET,
			limit:EVENTS_LIMIT
		});
}

export const filters = (filters = setInitState(), action) => {
    const {type, payload, response} = action;

    switch (type) {
		case LOAD_ALL_ARTICLES+SUCCESS :
            return filters = Object.assign(filters, 
	            {
	            	offset: payload.offset,
					limit: payload.limit
	            });

        case CHANGE_CATEGORIES : 
			return filters = Object.assign(filters, 
				{ categoryId: payload.id} );
		
        case CHANGE_USERS : 
			return filters = Object.assign(filters, 
				{ userId: payload.id} );
	}
    return filters;
}

export function getCategory(id){
	const {category} = store.getState();
	const {categoryId} = store.getState().filters;
	let curId = (!id) ? categoryId : id;

	const selCategos = category.filter((item) => item.id === curId);
	if (selCategos[0]) 
		return selCategos[0].name;
	return null;
}

export function getUser(id){
	const {user} = store.getState();
	const {userId} = store.getState().filters;
	let curId = (!id) ? userId : id;

	const selUsers = user.filter((item) => item.id === curId);
	if (selUsers[0]) 
		return selUsers[0].name;
	return null;
}


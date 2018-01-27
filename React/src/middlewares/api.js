import {START,SUCCESS,FAIL} from '../constants'

const api = store => next => action => {
	const {callAPI,type} = action;
	if (!callAPI) return next(action);

	const startAction = Object.assign({}, action, {type:type+START});
	next(startAction);

	setTimeout(() => {
		fetch(callAPI)
			.then(res => res.json())
			.then(response => {
				const loadAction = Object.assign({}, action, 
					{response}, {type:type+SUCCESS});
				return next(loadAction);
			})
			.catch(error => {
				const errAction = Object.assign({}, action, 
					{type:type+FAIL}, {error});
				return next(errAction);

			});
	},2000);
}

export default api;

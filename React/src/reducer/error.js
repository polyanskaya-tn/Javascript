import {LOAD_ALL_ARTICLES, LOAD_CATEGORIES, START, FAIL,
	DEF_ARTICLES, DEF_CATEGORY} from '../constants'

function setInitState() {
	return [ 
		{
			component: DEF_ARTICLES,
			error: ''
		},
		{ 
			component: DEF_CATEGORY,
			error: ''
		}
	];
}

function getNewError(err, component, error) {
	if (!component || !err) return err;
	if (!error) return setInitState();
	if (!error.message) error.message = 'Unknown error';

	var newerr = err.map(function(elem) {

  		if (elem.component === component) 
  			//new object
  			return Object.assign({}, 
  				{component, error:error.message});

		//only copy
		return Object.assign({}, elem);
	});
	return newerr;
}

export const error = (err = setInitState(), action) => {
	const {type, error} = action;
	switch (type) {
		case LOAD_ALL_ARTICLES+START :
			return  getNewError( err, DEF_ARTICLES, null);

		case LOAD_CATEGORIES+START : 
			return  getNewError( err, DEF_CATEGORY, null);

		case LOAD_ALL_ARTICLES+FAIL: 
			return  getNewError( err,DEF_ARTICLES, error);

		case LOAD_CATEGORIES+FAIL : 
			return  getNewError( err, DEF_CATEGORY, error);
	}
	return err;
}

export function getErrorMessage(error, component) {
	if (!error) return '';
	let elem = error.filter(elem => elem.component === component);
	return elem[0].error;
}

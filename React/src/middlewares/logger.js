import store from '../store/index'

const logger = store => next => action => {
	console.log('--','dispatching',action);
	console.log('--','state before: ', store.getState());
	next(action);
	console.log('--','state after: ', store.getState());
}

export default logger;
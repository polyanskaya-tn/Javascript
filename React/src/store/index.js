import {createStore, applyMiddleware} from 'redux'
import reducer from '../reducer/index'
import api from '../middlewares/api'
import logger from '../middlewares/logger'

const middleware = applyMiddleware(api , logger);

const store = createStore(reducer, middleware);

//dev only
window.store = store;

export default store;

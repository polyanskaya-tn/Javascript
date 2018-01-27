import React, {Component} from 'react'
import ReactDOM from 'react-dom'
import {Provider} from 'react-redux'
import store from './store/index'
import App from './components/App'
import Filters from './components/Filters/Filters'

ReactDOM.render(
	<Provider store={store}>
		<App/>
	</Provider>,
	document.getElementById('events')
);

ReactDOM.render(
	<Provider store={store}>
		<Filters/>
	</Provider>,
	document.getElementById('filters')
);


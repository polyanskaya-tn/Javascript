import React, {Component} from 'react'
import Select from 'react-select'
import {connect} from 'react-redux'
import {loadUsers, changeUsers, loadAllArticles} from '../../AC/index'
import {EVENTS_OFFSET} from '../../constants'
import {getCategory, getUser} from '../../reducer/filters.js'
import 'react-select/dist/react-select.css'

class SelectFilter extends Component {

	constructor (props) {
		super(props);
		this.handleChange = this.handleChange.bind(this);
	}

	componentDidMount(){
	    this.props.loadUsers();
	}

	render() {
		const options=this.props.users.map( 
				(user) => ({label: user.name, value: user.id})
			);
	
		return (
			<div id="select">
				<Select options={options} value={this.props.selection}  
					onChange={this.handleChange}/>
			</div>
		)
	}

	handleChange(selection) {
		this.props.changeUsers(selection.value);

		this.props.loadAllArticles(
			getCategory(),
			getUser(selection.value),
			EVENTS_OFFSET, 
			this.props.limit
		);

		this.setState({selection});
	}
}

export default connect(state =>({
	users: state.user,
	limit: state.filters.limit,
	selection: state.filters.user
}), {loadUsers, changeUsers, loadAllArticles} ) (SelectFilter);



import React, {Component} from 'react'
import {connect} from 'react-redux'
import {changeCategories,loadAllArticles} from '../../AC/index'
import {EVENTS_OFFSET} from '../../constants'
import {getCategory, getUser} from '../../reducer/filters.js'

class Category extends Component {

	constructor (props) {
		super(props);
		this.handleChange = this.handleChange.bind(this);
	}

	render() {
		const {category} = this.props;
		return (
			<a href="#cat" onClick={this.handleChange}>
				{category.name} 
			</a>
		)
	}

	handleChange() {
		const {category, limit} = this.props;
		this.props.changeCategories(category.id);

		this.props.loadAllArticles(
			getCategory(category.id),
			getUser(),
			EVENTS_OFFSET, 
			limit
		);
	}
}

export default connect(state =>({
	limit: state.filters.limit
}), {changeCategories,loadAllArticles}) (Category);

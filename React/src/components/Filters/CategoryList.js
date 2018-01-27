import React, {Component} from 'react'
import {connect} from 'react-redux'
import Category from './Category'
import {loadCategories} from '../../AC/index'
import {getErrorMessage} from '../../reducer/error'
import {DEF_CATEGORY} from '../../constants'

class CategoryList extends Component {

	componentDidMount(){
	    this.props.loadCategories();
	}

	render() {
		const categoryElems = this.props.category.map( 
			(category) => {
				return ( 
					<li key={category.id}>
						<Category category={category}/>
					</li>
				)}
		);

		return (
			<ul>
				{categoryElems}
			</ul>
		)
	}
}

export default connect(state =>({
	category: state.category,
	error: getErrorMessage(state.error, DEF_CATEGORY)
}), {loadCategories}) (CategoryList);



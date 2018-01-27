import React, {Component} from 'react'
import {connect} from 'react-redux'
import {loadAllArticles} from '../AC/index'
import Article from './Article'
import Loader from './Loader'
import {getErrorMessage} from '../reducer/error'
import {getCategory, getUser} from '../reducer/filters.js'
import {EVENTS_OFFSET, EVENTS_LIMIT, DEF_ARTICLES} from '../constants'

class ArticleList extends Component {

	constructor (props) {
		super(props);
		this.handleUpdateLoad = this.handleUpdateLoad.bind(this);
		this.handleMoreEvents = this.handleMoreEvents.bind(this);
	}

	componentDidMount(){
		const {loading, loaded, loadAllArticles} = this.props;
		if (!loading && !loaded)
            loadAllArticles('', '', EVENTS_OFFSET, EVENTS_LIMIT);
	}

	render() {
		const {loading, error} = this.props;
		if (loading) 
			return <Loader/>;
		if (error !== '') 
			return (
				<div>
					Error: {error} <br/>
					<a href="#" onClick={this.handleUpdateLoad}>Refresh</a>
				</div>
			);

		const articleElements = this.props.articles.map( 
			(article) => <li key={article.id}><Article article={article}/> </li>
		);

		let linkMore = '';
		if (articleElements.length === this.props.limit)
			linkMore = (<a href="#" onClick={this.handleMoreEvents}>More</a>);
		if (!articleElements.length)
			linkMore = 'There are no more events';

		return (
			<div>
				<ul>
					{articleElements}
				</ul>
				{linkMore}
			</div>
		)
	}

	handleUpdateLoad() {
		this.props.loadAllArticles('', '', EVENTS_OFFSET, EVENTS_LIMIT);
	}

	handleMoreEvents() {
        const {offset, limit, loadAllArticles} = this.props;

        loadAllArticles(
			getCategory(),
			getUser(),
			offset + limit, 
			limit
		);
	}

}

export default connect(state =>({
	articles: state.articles,
    offset: state.filters.offset,
	limit: state.filters.limit,
	loading:  state.loading,
	loaded:   state.loaded,
	category: state.filters.category,
	error: getErrorMessage(state.error, DEF_ARTICLES)
}), {loadAllArticles}) (ArticleList);


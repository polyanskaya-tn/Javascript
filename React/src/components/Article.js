import React, {Component} from 'react'

class Article extends Component {

	render() {
		const {article} = this.props;
		const authors = article.author.join(', ');

		return (
			<div>
				<p className="header">{article.title}</p>
				<p>{article.text}</p>
				<div>
					<div className="author">{authors}</div>
					<div className="pdate">{article.date}</div>
					<div className="clear"></div>
				</div>
			</div>
		)
	}
}

export default Article;

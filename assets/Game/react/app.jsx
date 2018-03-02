'use strict';

require('./app.scss');

const React = require('react');
const ReactDOM = require('react-dom');

class Hello extends React.Component {
    render () {
        return <div className='message-box'>
            Hello {this.props.name}
        </div>
    }
}

ReactDOM.render(<Hello name='John' />, document.getElementById('app'));
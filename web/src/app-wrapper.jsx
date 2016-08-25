import React from 'react';
import BigRocksApp from './big-rocks-app';
import Signin from './components/signin';
import Api from './lib/api';

export default class AppWrapper extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  onSignin(attrs) {
    new Api({}).signIn(attrs)
      .then(currentUser => this.setState({ currentUser }))
      .catch(loginError => this.setState({ loginError }));
  }

  render() {
    if (!this.state.currentUser) { return (<Signin error={this.state.loginError} onSubmit={this.onSignin.bind(this)}/>); }

    return (
      <BigRocksApp currentUser={this.state.currentUser}/>
    );
  }
}

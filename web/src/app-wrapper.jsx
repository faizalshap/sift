import React from 'react';
import BigRocksApp from './big-rocks-app';
import Signin from './components/signin';
import Api from './lib/api';

export default class AppWrapper extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      currentUser: JSON.parse(localStorage.getItem('currentUser'))
    };
  }

  onSignin(attrs) {
    new Api({}).signIn(attrs)
      .then(currentUser => {
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        this.setState({ currentUser });
      })
      .catch(loginError => this.setState({ loginError }));
  }

  render() {
    if (!this.state.currentUser) { return (<Signin error={this.state.loginError} onSubmit={this.onSignin.bind(this)}/>); }

    return (
      <BigRocksApp currentUser={this.state.currentUser}/>
    );
  }
}

import React from 'react';
import {hot} from 'react-hot-loader/root';
import Header from './components/Header';
import Routerview from './router/index';

function App() {
	return (
		<>
			<Header />

			<div className="wrap">
				<Routerview />
			</div>
		</>
	);
}

export default hot(App);

import React from 'react';
import { HashRouter as Router, Route, Switch } from 'react-router-dom';
import Settings from '../Pages/settings';
import GenerateDoc from '../Pages/generatedoc';
import Transactions from '../Pages/transactions';

const routes = [
  {
    path: '/',
    component: Transactions,
    exact: true,
  },
  {
    path: '/settings',
    component: Settings,
  },
  {
    path: '/generate-doc',
    component: GenerateDoc,
  },
];

/**
 * Render all routes
 */
function Routerview() {
  return (
    <>
      <Router>
        <Switch>
          {routes.map((route, i) => (
            <RenderRoute key={i} {...route} />
          ))}
        </Switch>
      </Router>
    </>
  );
}

/**
 * Render route component matching with path
 *
 * @param {*} route
 */
function RenderRoute(route) {
  if ( route.exact ) {
    return <Route path={route.path} exact component={route.component} />;  
  }

  return <Route path={route.path} component={route.component} />;
}

export default Routerview;

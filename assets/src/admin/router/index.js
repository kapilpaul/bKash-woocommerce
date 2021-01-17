import React from "react";
import {
  BrowserRouter as Router,
  useLocation,
} from "react-router-dom";
import Settings from "../Pages/settings";

const routes = [
  {
    path: "#/settings",
    component: Settings,
  },
];

/**
 * Render all routes
 */
function Routerview() {
  return (
    <>
      {routes.map((route, i) => (
        <RenderRoute key={i} {...route} />
      ))}
    </>
  );
}

/**
 * Render route component matching with path
 * 
 * @param {*} route 
 */
function RenderRoute(route) {
  let query = useQuery();

  if (query.get("page") === "dc-bkash" && useLocation().hash === route.path) {
    return <route.component />;
  }

  return "";
}

function useQuery() {
  return new URLSearchParams(useLocation().search);
}

export default Routerview;
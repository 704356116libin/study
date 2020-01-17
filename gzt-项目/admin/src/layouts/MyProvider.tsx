import React from 'react';

const myContext = React.createContext({
  collapsed: false,
  toggleCollapsed: () => { }
});

export default function MyProvider(props: any) {

  const { children, ...restProps } = props;

  return (
    <myContext.Provider value={{ ...restProps }}>
      {props.children}
    </myContext.Provider>
  )
}
export { myContext }
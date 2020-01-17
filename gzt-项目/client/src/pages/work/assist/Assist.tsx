import * as React from 'react';

const { useEffect } = React;

interface AssistProps extends React.Props<{}> {
  history: any
}

/**
 * 协助
 */
export default function Assist({
  children
}: AssistProps) {

  useEffect(() => {
    // todo ...
  })

  return <>{children}</>

}

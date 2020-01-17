import React from 'react';
import {
  Chart, Geom, Axis, Tooltip, Coord, Guide
} from 'bizcharts';
import DataSet from "@antv/data-set";

const { DataView } = DataSet;
const { Html } = Guide;
const dv = new DataView();

const cols = {
  percent: {
    formatter: (val: any) => {
      val = val * 100 + "%";
      return val;
    }
  }
};

export interface SmsData {
  dataSource: any;
}

export default function SmsData(props: SmsData) {

  const { dataSource } = props;
  if (!dataSource) {
    return null
  }
 const used = dataSource.use_number || 0;
  
  const data = [
    { item: "已用条数", count: used },
    { item: "剩余条数", count: dataSource.type_number - used }
  ];

  dv.source(data).transform({
    type: "percent",
    field: "count",
    dimension: "item",
    as: "percent"
  });

  return (
    <Chart
      width={400}
      height={300}
      data={dv}
      padding={40}
      scale={cols}
      forceFit
    >
      <Coord type="theta" radius={1} innerRadius={0.6} />
      <Axis name="percent" />
      <Tooltip
        showTitle={false}
        itemTpl="<li><span style=&quot;background-color:{color};&quot; class=&quot;g2-tooltip-marker&quot;></span>{name}: {value}</li>"
      />
      <Guide>
        <Html
          position={["50%", "50%"]}
          html={`<div style=&quot;color:#8c8c8c;font-size:1.16em;text-align: center;width: 120px;overflow:hidden&quot;>短信剩余<br><span style=&quot;color:#262626;font-size:2em&quot;>${data.filter(({ item }) => item === '剩余条数')[0].count}</span>条</div>`}
          alignX="middle"
          alignY="middle"
        />
      </Guide>
      <Geom
        type="intervalStack"
        position="percent"
        color={['item', ['rgb(248, 189, 87)', 'rgb(255, 155, 212)']]}
        tooltip={[
          "item*percent",
          (items, percent) => {
            return {
              name: items,
              value: `${data.filter(({ item }) => item === items)[0].count}条`
            };
          }
        ]}
        style={{
          lineWidth: 1,
          stroke: "#fff"
        }}
      />
    </Chart>
  )
}
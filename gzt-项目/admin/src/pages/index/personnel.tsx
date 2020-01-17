import React from 'react';
import {
  Chart, Geom, Axis, Tooltip, Legend, Coord
} from 'bizcharts';
import DataSet from "@antv/data-set";

const { DataView } = DataSet;
// const { Html } = Guide;
const dv = new DataView();

const cols = {
  percent: {
    formatter: (val: any) => {
      val = (val * 100).toFixed(2) + "%";
      return val;
    }
  }
};

export interface SmsData {
  dataSource: any;
}

export default function PersonnelData(props: SmsData) {

  const { dataSource } = props;
  if (!dataSource) {
    return null
  }

  const data = [
    { item: "员工人数", count: dataSource.staff_number.use_number },
    { item: "合作伙伴数量", count: dataSource.partner.use_number },
    { item: "外部联系人数", count: dataSource.external_contact.use_number }
  ];

  dv.source(data).transform({
    type: "percent",
    field: "count",
    dimension: "item",
    as: "percent"
  });

  const height = 300;

  return (
    <Chart
      width={400}
      height={height}
      data={dv}
      padding={[40, 90, 40, 0]}
      scale={cols}
      forceFit
    >
      <Coord type="theta" radius={1} innerRadius={0.6} />
      <Axis name="percent" />
      <Legend
        position="right"
        offsetY={-height / 2 + 80}
        offsetX={-40}
        itemFormatter={(val) => {
          return `${val}: ${data.filter(({ item, count }) => item === val)[0].count}人`; // val 为每个图例项的文本值
        }}
      />
      <Tooltip
        showTitle={false}
        itemTpl="<li><span style=&quot;background-color:{color};&quot; class=&quot;g2-tooltip-marker&quot;></span>{name}: {value}</li>"
      />
      {/* <Guide>
        <Html
          position={["50%", "50%"]}
          html={`<div style=&quot;color:#8c8c8c;font-size:1.16em;text-align: center;width: 120px;overflow:hidden&quot;>短信剩余<br><span style=&quot;color:#262626;font-size:2em&quot;>${dataSource.type_number - 0}</span>条</div>`}
          alignX="middle"
          alignY="middle"
        />
      </Guide> */}
      <Geom
        type="intervalStack"
        position="percent"
        color="item"
        tooltip={[
          "item*percent",
          (item, percent) => {
            return {
              name: item,
              value: `${data.filter(({ item }) => item === item)[0].count}人`
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
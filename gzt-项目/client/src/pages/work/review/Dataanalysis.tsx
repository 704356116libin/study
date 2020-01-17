import * as React from 'react';
import { Layout, Row, Col } from 'antd';
import {
  Chart, Geom, Axis, Tooltip, Legend, Coord, Label, Guide,
} from 'bizcharts';
import DataSet from "@antv/data-set";

const { DataView } = DataSet;
const { Html } = Guide;

// 数据源
const data = [
  { genre: 'LOL', sold: 275, income: 2300 },
  { genre: '绝地求生', sold: 115, income: 667 },
  { genre: 'DNF', sold: 120, income: 982 },
  { genre: 'QQ炫舞', sold: 350, income: 5271 },
  { genre: 'QQ飞车', sold: 150, income: 3710 }
];

// 定义度量
const cols = {
  sold: { alias: '最高同时在线人数' },
  genre: { alias: '游戏种类' }
};

// 单图表主题配置
const theme = {
  axis: {
    left: {
      title: {
        textStyle: {
          fill: '#666'
        }
      }
    },
    bottom: {
      title: {
        textStyle: {
          fill: '#666'
        }
      }
    }

  }
}
const dv = new DataView();
const data2 = [
  { item: "事例一", count: 40 },
  { item: "事例二", count: 21 },
  { item: "事例三", count: 17 },
  { item: "事例四", count: 13 },
  { item: "事例五", count: 9 }
];

dv.source(data2).transform({
  type: "percent",
  field: "count",
  dimension: "item",
  as: "percent"
});

const cols2 = {
  percent: {
    formatter: (val: any) => {
      val = val * 100 + "%";
      return val;
    }
  }
};
/**
 * 数据展示
 */
export default class Dataanalysis extends React.Component<any>{

  componentDidMount() {
  }

  render() {
    return (
      <Layout className="review-dataanalysis">
        <Row>
          <Col span={12}>
            <Chart width={600} height={400} data={data} scale={cols} theme={theme}>
              <Axis name="genre" position="bottom" />
              <Axis name="sold" position="left" />
              <Legend position="top" offsetY={25} />
              <Tooltip />
              <Geom type="interval" position="genre*sold" color="genre" />
            </Chart>

          </Col>
          <Col span={12}>
            <Chart
              width={600}
              height={400}
              data={dv}
              padding={[80, 100, 80, 80]}
              scale={cols2}
            >
              <Coord type="theta" radius={1} innerRadius={0.6} />
              <Axis name="percent" />
              <Legend
                position="right"
                offsetY={-400 / 2 + 120}
                offsetX={0}
              />
              <Tooltip
                showTitle={false}
                itemTpl="<li><span style=&quot;background-color:{color};&quot; class=&quot;g2-tooltip-marker&quot;></span>{name}: {value}</li>"
              />
              <Guide>
                {/* <Text
                  position={["50%", "50%"]}
                  content="111111"
                  offsetX={-20}
                /> */}
                <Html
                  position={["50%", "50%"]}
                  html="<div style=&quot;color:#8c8c8c;font-size:1.16em;text-align: center;width: 120px;overflow:hidden&quot;>总金额<br><span style=&quot;color:#262626;font-size:2em&quot;>15781</span><br>万元</div>"
                  alignX="middle"
                  alignY="middle"
                />
              </Guide>
              <Geom
                type="intervalStack"
                position="percent"
                color="item"
                tooltip={[
                  "item*percent",
                  (item, percent) => {
                    percent = percent * 100 + "%";
                    return {
                      name: item,
                      value: percent
                    };
                  }
                ]}
                style={{
                  lineWidth: 1,
                  stroke: "#fff"
                }}
              >
                <Label
                  content="percent"
                  formatter={(val, item) => {
                    return item.point.item + ": " + val;
                  }}
                />
              </Geom>
            </Chart>

          </Col>
        </Row>

      </Layout>
    );
  }
}

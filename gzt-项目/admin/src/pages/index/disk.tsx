// data-set 可以按需引入，除此之外不要引入别的包
import React from 'react';
import { Chart, Geom, Guide } from 'bizcharts';
import handleSize from '../../utils/handleSize';

const { Text } = Guide;





export interface DiskDataProps {
  dataSource: any;
}

export default function DiskData(props: DiskDataProps) {
  const { dataSource } = props;
  if (!dataSource) {
    return null
  }

  const data = [{
    gender: '云盘容量',
    path: 'M381.759 0h292l-.64 295.328-100.127-100.096-94.368 94.368C499.808 326.848 512 369.824 512 415.712c0 141.376-114.56 256-256 256-141.376 0-256-114.624-256-256s114.624-256 256-256c48.8 0 94.272 13.92 133.12 37.632l93.376-94.592L381.76 0zM128.032 415.744c0 70.688 57.312 128 128 128s128-57.312 128-128-57.312-128-128-128-128 57.312-128 128z',
    value: dataSource.type_number - dataSource.use_number
  }];

  const scale = {
    value: {
      min: 0,
      max: dataSource.type_number
    },
  };

  const height = 300;

  return (
    <Chart
      height={height}
      data={data}
      scale={scale}
      padding={[0, 40, 0, 40]}
      forceFit
    >
      <Geom
        type="interval"
        position="gender*value"
        color={['gender', 'rgb(163, 221, 248)']}
        shape="liquid-fill-gauge"
        style={{
          lineWidth: 5,
          opacity: 0.75,
        }}
      />
      <Guide>
        {
          data.map(
            row => (
              <>
                <Text
                  content={`剩余容量${handleSize(row.value).join('')}`}
                  top
                  position={{
                    gender: row.gender,
                    value: row.value / 2,
                  }}
                  style={{
                    opacity: 0.75,
                    fontSize: height / 15,
                    textAlign: 'center',
                  }}
                />
                <Text
                  content={`网盘总量${handleSize(dataSource.type_number).join('')}`}
                  top
                  position={{
                    gender: row.gender,
                    value: row.value / 2,
                  }}
                  offsetY={-50}
                  style={{
                    opacity: 0.75,
                    fontSize: height / 15,
                    textAlign: 'center',
                    fill: '#000'
                  }}
                />
              </>
            ))
        }
      </Guide>
    </Chart>
  )

}

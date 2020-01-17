import React, { useState, forwardRef, useImperativeHandle } from 'react';
import { Select } from 'antd';
const Option = Select.Option;
interface selectLinkageProps {
  provinceData: any,
  cityData: any,
  countyData: any,
  onChange?: any
}

function SelectLinkage(props: selectLinkageProps, ref: React.Ref<any>) {

  useImperativeHandle(ref, () => ({}));

  const {
    provinceData,
    cityData,
    countyData,
    onChange
  } = props;
  const defaultProvince = provinceData[0];//省
  const defaultCity = cityData[defaultProvince];//市
  const defaultCounty = countyData[defaultProvince][defaultCity[0]];//县

  const [provinceValue, setProvinceValue] = useState(defaultProvince);//省份默认值

  const [cityItem, setCityItem] = useState(defaultCity);//市
  const [cityValue, setCityValue] = useState(defaultCity[0]);//市默认值

  const [countyItem, setCountyItem] = useState(defaultCounty);//县
  const [countyValue, setCountyValue] = useState(defaultCounty[0]);//县默认值

  // const selectLinkage = [defaultProvince, defaultCity, defaultCounty];//省市县默认数据

  /**
   * 省份
   */
  function handleProvinceChange(value: any) {
    setProvinceValue(value);

    setCityItem(cityData[value]);
    setCityValue(cityData[value][0]);

    setCountyItem(countyData[value][cityData[value][0]]);
    setCountyValue(countyData[value][cityData[value][0]][0]);

    const selectLinkage = [value, cityData[value][0], countyData[value][cityData[value][0]][0]];
    onChange && onChange(selectLinkage);
  }
  /**
   *  市区
   */
  function onCityChange(value: any) {
    setCityValue(value);
    setCountyItem(countyData[provinceValue][value]);
    setCountyValue(countyData[provinceValue][value][0]);

    const selectLinkage = [provinceValue, value, countyData[provinceValue][value][0]];
    onChange && onChange(selectLinkage);
  }
  /**
   * 县
   */
  function onCountyChange(value: any) {
    setCountyValue(value);

    const selectLinkage = [provinceValue, cityValue, value];
    onChange && onChange(selectLinkage);

  }
  return (
    <>
      <Select
        defaultValue={provinceValue}
        style={{ width: 180 }}
        onChange={handleProvinceChange}
      >
        {provinceData.map((province: any, index: any) => <Option key={index} value={province} >{province}</Option>)}
      </Select>
      <Select
        style={{ width: 180, margin: '0 10px' }}
        value={cityValue}
        onChange={onCityChange}
      >
        {cityItem.map((city: any, index: any) => <Option key={index} value={city}>{city}</Option>)}
      </Select>

      <Select
        style={{ width: 180 }}
        value={countyValue}
        onChange={onCountyChange}
      >
        {countyItem.map((county: any, index: any) => <Option key={index} value={county}>{county}</Option>)}
      </Select>
    </>
  )
}

export default forwardRef(SelectLinkage)

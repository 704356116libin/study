import React, { useState, useEffect } from 'react';
import { Row, Col, Button, Modal } from 'antd';
import TextLabel from '../../../../../components/textLabel';
import DocNumberDropdown from './dropdown-docNumber';
import moment from 'moment';
import './index.scss'
import { get } from '../../../../../utils/request';

export interface DocNumberProps {
  onOk: Function;
}

export interface Rule {
  type: 'label' | 'date' | 'plus',
  title: string;
  value: string;
}

export default function DocNumber(props: DocNumberProps) {

  const { onOk } = props;

  /** 上限 */
  const UPPER_LIMIT = 9;

  const [upperLimit, setUpperLimit] = useState(false);

  const [rules, setRules]: [Rule[], any] = useState([
    {
      type: 'label',
      title: '标签',
      value: 'BQ'
    },
    {
      type: 'date',
      title: '日期',
      value: moment().format('YYYY'),
      rule: '年'
    },
    {
      type: 'plus',
      title: '增长值',
      value: '0001',
      rule: {
        dight: 4,
        startNumber: 1,
        step: 1,
      }
    }
  ])

  useEffect(() => {
    (async () => {
      const result = await get('/api/c_pst_report_number_get');
      if (result.status === 'success') {
        setRules(result.data)
      }
    })()
  }, [])

  function handleAddRule() {
    if (rules.length > UPPER_LIMIT) {
      return
    }
    // 是否到达上限
    setUpperLimit(rules.length === UPPER_LIMIT);
    // 默认新增标签
    setRules([
      ...rules,
      {
        type: 'label',
        title: '标签',
        value: 'BQ'
      }
    ])
  }
  /** 删除规则 */
  function handleClose(k: number) {
    const nextRules = [...rules];
    nextRules.splice(k, 1);
    setRules(nextRules)
  }
  /** 更新规则 */
  function handleDocNumberChange(rule: Rule, k: number) {
    const nextRules = [...rules];
    const plusIndex = nextRules.findIndex(({ type }) => type === 'plus');
    if (rule.type === 'plus' && plusIndex > 0 && plusIndex !== k) {
      Modal.warning({
        title: '提醒',
        content: '仅支持一个增长值类型',
      });
      return
    }
    nextRules.splice(k, 1, rule);
    setRules(nextRules)
  }

  function handleOk() {
    onOk && onOk(rules);
  }

  return (
    <div>
      <Row type="flex" style={{ padding: '12px 0' }}>
        <Col span={3} style={{ textAlign: 'right' }}><TextLabel text="编号示例" /></Col>
        <Col span={21}>
          {rules.map(({ value }) => value)}
        </Col>
      </Row>
      <Row type="flex" style={{ padding: '12px 0' }}>
        <Col span={3} style={{ textAlign: 'right' }}><TextLabel text="文号规则" /></Col>
        <Col span={21}>
          {
            rules.map((rule, k) => (
              <DocNumberDropdown
                key={k}
                value={rule}
                onClose={() => handleClose(k)}
                onChange={(rule: any) => handleDocNumberChange(rule, k)}
              />
            ))
          }
          {
            !upperLimit && <Button icon="plus" style={{ width: 57, height: 57 }} onClick={handleAddRule} />
          }
        </Col>
      </Row>
      <Row>
        <Col span={21} offset={3}>
          <Button type="primary" onClick={handleOk}>设置</Button>
        </Col>
      </Row>
    </div>
  )
}

// 基础表单组件

import React, { useState } from 'react';
import { Input, Checkbox, Button, Icon } from 'antd';

/**
 * createBaseItemWraper方法给回调函数传递的dom元素
 */
export type CreateBaseItemBodyParams = (
  component: HTMLDivElement,
  remove: HTMLDivElement,
  cover: any,
  field: HTMLDivElement
) => void;

/**
 * 创建基础表单外壳
 * @param createBaseItemBody 回调函数，接收一些创建基础表单展示需要的公共dom元素
 */
function createBaseItemWraper(createBaseItemBody: CreateBaseItemBodyParams) {

  const component = document.createElement('div');
  const remove = document.createElement('div');
  const cover = document.createElement('div');
  const field = document.createElement('div');

  component.className = 'cf-component';
  remove.className = 'cf-remove';
  cover.className = 'cf-cover';
  field.className = 'cf-field';

  remove.innerHTML = '&times';

  // 执行回调函数，往壳子里塞东西
  createBaseItemBody(component, remove, cover, field);

  component.appendChild(remove);
  component.appendChild(cover);
  component.appendChild(field);

  return component
}

const INPUT = {
  name: '单行文本框',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {
      let labelText = '单行文本框';
      let placeholderText = '请输入';
      let required = false;

      if (initField) {
        labelText = initField.label;
        placeholderText = initField.placeholder;
        required = initField.required;
      }

      const label = document.createElement('label');
      const placeholder = document.createElement('span');

      label.className = 'cf-field-label';
      placeholder.className = 'cf-field-placeholder';

      required ? label.classList.add('required') : label.classList.remove('required');

      cover.dataset.itemType = 'INPUT';
      label.textContent = labelText;
      placeholder.textContent = placeholderText;

      cover.formInfo = {
        type: 'INPUT',
        field: {
          label: labelText,
          placeholder: placeholderText,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb();
      })

      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'INPUT',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            placeholder: {
              value: placeholder.textContent,
              onChange(e: any, formInfo: any) {
                placeholder.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
                cover.formInfo = formInfo;
              }
            }
          }
        })
      })
      field.appendChild(label);
      field.appendChild(placeholder);
    })

  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, placeholder, required } = field;

    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'INPUT',
        field: {
          label: e.target.value,
          placeholder: placeholder.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'INPUT',
        field
      });
    }

    function onPlaceholderChange(e: any) {

      field.placeholder.value = e.target.value;
      placeholder.onChange(e, {
        type: 'INPUT',
        field: {
          label: label.value,
          placeholder: e.target.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'INPUT',
        field
      });

    }

    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'INPUT',
        field: {
          label: label.value,
          placeholder: placeholder.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'INPUT',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          单行文本框
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-placeholder">
            <div className="description">
              <span className="name">提示文字</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={20} value={placeholder.value} onChange={onPlaceholderChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const TEXTAREA = {
  name: '多行文本框',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '多行文本框';
      let placeholderText = '请输入';
      let required = false;

      if (initField) {
        labelText = initField.label;
        placeholderText = initField.placeholder;
        required = initField.required;
      }

      const label = document.createElement('label');
      const placeholder = document.createElement('span');

      label.className = 'cf-field-label';
      placeholder.className = 'cf-field-placeholder';

      required ? label.classList.add('required') : label.classList.remove('required');

      placeholder.style.paddingBottom = '16px';

      cover.dataset.itemType = 'TEXTAREA';
      label.textContent = labelText;
      placeholder.textContent = placeholderText;

      cover.formInfo = {
        type: 'TEXTAREA',
        field: {
          label: labelText,
          placeholder: placeholderText,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb()
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'TEXTAREA',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            placeholder: {
              value: placeholder.textContent,
              onChange(e: any, formInfo: any) {
                placeholder.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(placeholder);
    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, placeholder, required } = field;
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'TEXTAREA',
        field: {
          label: e.target.value,
          placeholder: placeholder.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'TEXTAREA',
        field
      });
    }

    function onPlaceholderChange(e: any) {

      field.placeholder.value = e.target.value;
      placeholder.onChange(e, {
        type: 'TEXTAREA',
        field: {
          label: label.value,
          placeholder: e.target.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'TEXTAREA',
        field
      });

    }

    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'TEXTAREA',
        field: {
          label: label.value,
          placeholder: placeholder.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'TEXTAREA',
        field
      });
    }
    return (
      <div>
        <h3 className="cf-customizer-type">
          多行文本框
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-placeholder">
            <div className="description">
              <span className="name">提示文字</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={20} value={placeholder.value} onChange={onPlaceholderChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const RADIO = {
  name: '单选框',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {
      let labelText = '单选框';
      let required = false;
      let radioOptions = [
        {
          key: 1,
          value: '选项1'
        },
        {
          key: 2,
          value: '选项2'
        },
        {
          key: 3,
          value: '选项3'
        }
      ]
      if (initField) {
        labelText = initField.label;
        required = initField.required;
        radioOptions = initField.radioOptions;
      }




      const label = document.createElement('label');
      const radio = document.createElement('span');

      label.className = 'cf-field-label';
      radio.className = 'cf-field-radio';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'RADIO';
      label.textContent = labelText;
      radio.textContent = '请选择';

      cover.formInfo = {
        type: 'RADIO',
        field: {
          label: labelText,
          radioOptions,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb();
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'RADIO',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            radioOptions: {
              value: radioOptions,
              onChange(e: any, formInfo: any) {
                // label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
                cover.formInfo = formInfo;
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(radio);

    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, required, radioOptions } = field;

    const [deletedKeys, setdeletedKeys]: [number[], any] = useState([])
    const [options, setOptions] = useState(radioOptions.value);

    function remove(k: number) {
      // 至少保留一个
      if (options.length === 1) {
        return;
      }
      setdeletedKeys(deletedKeys.concat(k));
      const newOptions = options.filter(({ key }: any) => key !== k);
      setOptions(newOptions);

      field.radioOptions.value = newOptions;
      radioOptions.onChange(null, {
        type: 'RADIO',
        field: {
          label: label.value,
          radioOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'RADIO',
        field
      });
    }
    function add() {

      let key: number;
      if (deletedKeys.length !== 0) {
        key = Math.min(...deletedKeys);
        setdeletedKeys(deletedKeys.filter((k: number) => k !== key));
      } else {
        key = options.length + 1
      }

      const newOptions = options.concat({
        key,
        value: `选项${key}`
      });
      setOptions(newOptions);

      field.radioOptions.value = newOptions;
      radioOptions.onChange(null, {
        type: 'RADIO',
        field: {
          label: label.value,
          radioOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'RADIO',
        field
      });
    }
    function onRadioChange(e: any, k: number) {
      const newOptions = options.map(({ key, value }: any) => key === k ? { key, value: e.target.value } : { key, value });
      setOptions(newOptions);
      field.radioOptions.value = newOptions;
      radioOptions.onChange(e, {
        type: 'RADIO',
        field: {
          label: label.value,
          radioOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'RADIO',
        field
      });
    }
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'RADIO',
        field: {
          label: e.target.value,
          radioOptions: options,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'RADIO',
        field
      });
    }

    function onRequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'RADIO',
        field: {
          label: label.value,
          radioOptions: options,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'RADIO',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          单选框
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-radio">
            <div className="description">
              <span className="name">选项</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              {options.map((item: any) => (
                <div key={item.key}>
                  <Input style={{ width: '180px', margin: '0 10px 10px 0' }} maxLength={12} value={item.value} onChange={(e) => onRadioChange(e, item.key)} />
                  {options.length > 1 ? (
                    <Icon
                      className="dynamic-delete-button"
                      type="minus-circle-o"
                      onClick={() => remove(item.key)}
                    />
                  ) : null
                  }
                </div>
              ))}
              <Button type="dashed" onClick={add} style={{ width: '60%' }}>
                <Icon type="plus" /> 添加选项
              </Button>
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onRequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const CHECKBOX = {
  name: '多选框',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '多选框';
      let required = false;
      let checkboxOptions = [{
        key: 1,
        value: '选项1'
      },
      {
        key: 2,
        value: '选项2'
      },
      {
        key: 3,
        value: '选项3'
      }];

      if (initField) {
        labelText = initField.label;
        required = initField.required;
        checkboxOptions = initField.checkboxOptions;
      }

      const label = document.createElement('label');
      const checkbox = document.createElement('span');

      label.className = 'cf-field-label';
      checkbox.className = 'cf-field-placeholder';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'CHECKBOX';
      label.textContent = labelText;
      checkbox.textContent = '请选择';

      cover.formInfo = {
        type: 'CHECKBOX',
        field: {
          label: labelText,
          checkboxOptions,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb();
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'CHECKBOX',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            checkboxOptions: {
              value: checkboxOptions,
              onChange(e: any, formInfo: any) {
                // label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
                cover.formInfo = formInfo;
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(checkbox);

    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, required, checkboxOptions } = field;

    const [deletedKeys, setdeletedKeys]: [number[], any] = useState([])
    const [options, setOptions] = useState(checkboxOptions.value);

    function remove(k: number) {
      // 至少保留一个
      if (options.length === 1) {
        return;
      }

      setdeletedKeys(deletedKeys.concat(k));
      const newOptions = options.filter(({ key }: any) => key !== k);
      setOptions(newOptions);

      field.checkboxOptions.value = newOptions;
      checkboxOptions.onChange(null, {
        type: 'CHECKBOX',
        field: {
          label: label.value,
          checkboxOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'CHECKBOX',
        field
      });
    }
    function add() {

      let key: number;
      if (deletedKeys.length !== 0) {
        key = Math.min(...deletedKeys);
        setdeletedKeys(deletedKeys.filter((k: number) => k !== key));
      } else {
        key = options.length + 1
      }

      const newOptions = options.concat({
        key,
        value: `选项${key}`
      });
      setOptions(newOptions);

      field.checkboxOptions.value = newOptions;
      checkboxOptions.onChange(null, {
        type: 'CHECKBOX',
        field: {
          label: label.value,
          checkboxOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'CHECKBOX',
        field
      });
    }
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'CHECKBOX',
        field: {
          label: e.target.value,
          checkboxOptions: options,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'CHECKBOX',
        field
      });
    }
    function onCheckboxChange(e: any, k: number) {
      const newOptions = options.map(({ key, value }: any) => key === k ? { key, value: e.target.value } : { key, value });
      setOptions(newOptions);
      field.checkboxOptions.value = newOptions;
      checkboxOptions.onChange(e, {
        type: 'CHECKBOX',
        field: {
          label: label.value,
          checkboxOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'CHECKBOX',
        field
      });
    }

    function onRequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'CHECKBOX',
        field: {
          label: label.value,
          checkboxOptions: options,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'CHECKBOX',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          多选框
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-checkbox">
            <div className="description">
              <span className="name">选项</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              {options.map((item: any) => (
                <div key={item.key}>
                  <Input style={{ width: '180px', margin: '0 10px 10px 0' }} maxLength={12} value={item.value} onChange={(e) => onCheckboxChange(e, item.key)} />
                  {options.length > 1 ? (
                    <Icon
                      className="dynamic-delete-button"
                      type="minus-circle-o"
                      onClick={() => remove(item.key)}
                    />
                  ) : null
                  }
                </div>
              ))}
              <Button type="dashed" onClick={add} style={{ width: '60%' }}>
                <Icon type="plus" /> 添加选项
              </Button>
            </div>

          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onRequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const DATEPICKER = {
  name: '日期',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '日期';
      let required = false;

      if (initField) {
        labelText = initField.label;
        required = initField.required;
      }

      const label = document.createElement('label');
      const date = document.createElement('date')

      label.className = 'cf-field-label';
      date.className = 'cf-field-date';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'DATEPICKER';
      label.textContent = labelText;
      date.textContent = '请选择日期';
      // 切换日期格式 todo......

      cover.formInfo = {
        type: 'DATEPICKER',
        field: {
          label: labelText,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb()
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'DATEPICKER',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                label.textContent = e.target.value;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(date);

    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, required } = field;
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'DATEPICKER',
        field: {
          label: e.target.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'DATEPICKER',
        field
      });
    }

    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'DATEPICKER',
        field: {
          label: label.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'DATEPICKER',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          日期
          </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>

        </div>
      </div>
    )
  }
}
const DATERANGE = {
  name: '日期区间',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '日期区间';
      let required = false;

      if (initField) {
        labelText = initField.label;
        required = initField.required;
      }

      const label = document.createElement('label');
      const range = document.createElement('date')

      label.className = 'cf-field-label';
      range.className = 'cf-field-range';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'DATERANGE';
      label.textContent = labelText;
      range.textContent = '开始日期 ~ 结束日期';
      // 切换日期格式 todo......

      cover.formInfo = {
        type: 'DATERANGE',
        field: {
          label: labelText,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb()
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'DATERANGE',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                label.textContent = e.target.value;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(range);
    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, required } = field;
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'DATERANGE',
        field: {
          label: e.target.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'DATERANGE',
        field
      });
    }

    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'DATERANGE',
        field: {
          label: label.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'DATERANGE',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          日期区间
          </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const NUMBER = {
  name: '数字',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '数字';
      let placeholderText = '请输入数字';
      let required = false;

      if (initField) {
        labelText = initField.label;
        placeholderText = initField.placeholder;
        required = initField.required;
      }

      const label = document.createElement('label');
      const placeholder = document.createElement('span');
      const unit = document.createElement('span');

      label.className = 'cf-field-label';
      placeholder.className = 'cf-field-placeholder';
      unit.className = 'cf-field-unit';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'NUMBER';
      label.textContent = labelText;
      placeholder.textContent = placeholderText;

      cover.formInfo = {
        type: 'NUMBER',
        field: {
          label: labelText,
          placeholder: placeholderText,
          unit: '',
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb()
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'NUMBER',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                label.textContent = e.target.value;
              }
            },
            placeholder: {
              value: placeholder.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                placeholder.textContent = e.target.value;
              }
            },
            unit: {
              value: unit.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                unit.textContent = e.target.value;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
              }
            }

          }
        })
      })

      field.appendChild(label);
      field.appendChild(unit);
      field.appendChild(placeholder);
    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, placeholder, required, unit } = field;
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'NUMBER',
        field: {
          label: e.target.value,
          placeholder: placeholder.value,
          unit: unit.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'NUMBER',
        field
      });
    }

    function onPlaceholderChange(e: any) {
      field.placeholder.value = e.target.value;
      placeholder.onChange(e, {
        type: 'NUMBER',
        field: {
          label: label.value,
          placeholder: e.target.value,
          unit: unit.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'NUMBER',
        field
      });

    }
    function onUnitChange(e: any) {
      field.unit.value = e.target.value;
      unit.onChange(e, {
        type: 'NUMBER',
        field: {
          label: label.value,
          placeholder: placeholder.value,
          unit: e.target.value,
          required: required.value.value
        }
      });
      setCurrentItemInfo({
        type: 'NUMBER',
        field
      });
    }
    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'NUMBER',
        field: {
          label: label.value,
          placeholder: placeholder.value,
          unit: unit.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'NUMBER',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          数字
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-placeholder">
            <div className="description">
              <span className="name">提示文字</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={20} value={placeholder.value} onChange={onPlaceholderChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-unit">
            <div className="description">
              <span className="name">单位</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={20} value={unit.value} onChange={onUnitChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const MONEY = {
  name: '金额',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '金额';
      let placeholderText = '请输入金额';
      let required = false;

      if (initField) {
        labelText = initField.label;
        placeholderText = initField.placeholder;
        required = initField.required;
      }

      const label = document.createElement('label');
      const placeholder = document.createElement('span');

      label.className = 'cf-field-label';
      placeholder.className = 'cf-field-placeholder';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'MONEY';
      label.textContent = labelText;
      placeholder.textContent = placeholderText;

      cover.formInfo = {
        type: 'MONEY',
        field: {
          label: labelText,
          placeholder: placeholderText,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb()
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'MONEY',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                label.textContent = e.target.value;
              }
            },
            placeholder: {
              value: placeholder.textContent,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                placeholder.textContent = e.target.value;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
              }
            },
            uppercaseNumber: {
              value: true,
              onChange(e: any, formInfo: any) {
                cover.formInfo = formInfo;
                e.target.checked ? console.log(true) : console.log(false)
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(placeholder);
    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, placeholder, required, uppercaseNumber } = field;
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'MONEY',
        field: {
          label: e.target.value,
          placeholder: placeholder.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'MONEY',
        field
      });
    }

    function onPlaceholderChange(e: any) {

      field.placeholder.value = e.target.value;
      placeholder.onChange(e, {
        type: 'MONEY',
        field: {
          label: label.value,
          placeholder: e.target.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'MONEY',
        field
      });
    }

    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'MONEY',
        field: {
          label: label.value,
          placeholder: placeholder.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'MONEY',
        field
      });
    }

    function onUppercaseNumberChange(e: any) {
      uppercaseNumber.onChange(e, {
        type: 'MONEY',
        field: {
          label: label.value,
          placeholder: placeholder.value,
          required: e.target.checked,
          uppercaseNumber: uppercaseNumber.value
        }
      });
      field.uppercaseNumber.value = e.target.checked;
      setCurrentItemInfo({
        type: 'MONEY',
        field
      });
    }
    return (
      <div>
        <h3 className="cf-customizer-type">
          金额
          </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-placeholder">
            <div className="description">
              <span className="name">提示文字</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={20} value={placeholder.value} onChange={onPlaceholderChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-uppercaseNumber">
            <div className="custom">
              <Checkbox checked={uppercaseNumber.value} onChange={onUppercaseNumberChange}>
                显示大写
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const SELECT = {
  name: '下拉选择框',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '下拉选择框';
      let required = false;
      let selectOptions = [
        {
          key: 1,
          value: '选项1'
        },
        {
          key: 2,
          value: '选项2'
        },
        {
          key: 3,
          value: '选项3'
        }
      ]
      if (initField) {
        labelText = initField.label;
        required = initField.required;
        selectOptions = initField.selectOptions;
      }

      const label = document.createElement('label');
      const select = document.createElement('span');

      label.className = 'cf-field-label';
      select.className = 'cf-field-radio';

      required ? label.classList.add('required') : label.classList.remove('required');


      cover.dataset.itemType = 'SELECT';
      label.textContent = labelText;
      select.textContent = '请选择';

      cover.formInfo = {
        type: 'SELECT',
        field: {
          label: labelText,
          selectOptions,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb();
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'SELECT',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            selectOptions: {
              value: selectOptions,
              onChange(e: any, formInfo: any) {
                // label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
                cover.formInfo = formInfo;
              }
            }
          }
        })
      })

      field.appendChild(label);
      field.appendChild(select);
    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, required, selectOptions } = field;

    const [deletedKeys, setdeletedKeys]: [number[], any] = useState([])
    const [options, setOptions] = useState(selectOptions.value);

    function remove(k: number) {
      // 至少保留一个
      if (options.length === 1) {
        return;
      }

      setdeletedKeys(deletedKeys.concat(k));
      const newOptions = options.filter(({ key }: any) => key !== k);
      setOptions(newOptions);
      field.selectOptions.value = newOptions;
      selectOptions.onChange(null, {
        type: 'SELECT',
        field: {
          label: label.value,
          selectOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'SELECT',
        field
      });
    }
    function add() {

      let key: number;
      if (deletedKeys.length !== 0) {
        key = Math.min(...deletedKeys);
        setdeletedKeys(deletedKeys.filter((k: number) => k !== key));
      } else {
        key = options.length + 1
      }

      const newOptions = options.concat({
        key,
        value: `选项${key}`
      });
      setOptions(newOptions);

      field.selectOptions.value = newOptions;
      selectOptions.onChange(null, {
        type: 'SELECT',
        field: {
          label: label.value,
          selectOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'SELECT',
        field
      });
    }
    function onSelectChange(e: any, k: number) {
      const newOptions = options.map(({ key, value }: any) => key === k ? { key, value: e.target.value } : { key, value });
      setOptions(newOptions);
      field.selectOptions.value = newOptions;
      selectOptions.onChange(e, {
        type: 'SELECT',
        field: {
          label: label.value,
          selectOptions: newOptions,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'SELECT',
        field
      });
    }
    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'SELECT',
        field: {
          label: e.target.value,
          selectOptions: options,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'SELECT',
        field
      });
    }

    function onRequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'SELECT',
        field: {
          label: label.value,
          selectOptions: options,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'SELECT',
        field
      });
    }

    return (
      <div>
        <h3 className="cf-customizer-type">
          下拉选择框
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-select">
            <div className="description">
              <span className="name">选项</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              {options.map((item: any) => (
                <div key={item.key}>
                  <Input style={{ width: '180px', margin: '0 10px 10px 0' }} maxLength={12} value={item.value} onChange={(e) => onSelectChange(e, item.key)} />
                  {options.length > 1 ? (
                    <Icon
                      className="dynamic-delete-button"
                      type="minus-circle-o"
                      onClick={() => remove(item.key)}
                    />
                  ) : null
                  }
                </div>
              ))}
              <Button type="dashed" onClick={add} style={{ width: '60%' }}>
                <Icon type="plus" /> 添加选项
              </Button>
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onRequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}
const ANNEX = {
  name: '附件',
  component: (initField: any, setCurrentItemInfo: any, romoveCb: () => void) => {
    return createBaseItemWraper((component, remove, cover, field) => {

      let labelText = '附件';
      let required = false;

      if (initField) {
        labelText = initField.label;
        required = initField.required;
      }

      const label = document.createElement('label');

      label.className = 'cf-field-label';

      required ? label.classList.add('required') : label.classList.remove('required');

      cover.dataset.itemType = 'ANNEX';
      label.textContent = labelText;

      cover.formInfo = {
        type: 'ANNEX',
        field: {
          label: labelText,
          required
        }
      }

      remove.addEventListener('click', () => {
        (component as any).parentNode.parentNode.removeChild(component.parentNode);
        romoveCb();
      })
      component.addEventListener('click', () => {
        setCurrentItemInfo({
          type: 'ANNEX',
          field: {
            label: {
              value: label.textContent,
              onChange(e: any, formInfo: any) {
                label.textContent = e.target.value;
                cover.formInfo = formInfo;
              }
            },
            required: {
              value: label.classList.contains('required'),
              onChange(e: any, formInfo: any) {
                e.target.checked ? label.classList.add('required') : label.classList.remove('required');
                cover.formInfo = formInfo;
              }
            }
          }
        })
      })

      field.appendChild(label);
    })
  },
  Customizer: ({ field, setCurrentItemInfo }: any) => {
    const { label, required } = field;

    function onLabelChange(e: any) {
      field.label.value = e.target.value;
      label.onChange(e, {
        type: 'ANNEX',
        field: {
          label: e.target.value,
          required: required.value
        }
      });
      setCurrentItemInfo({
        type: 'ANNEX',
        field
      });
    }

    function onrequiredChange(e: any) {

      field.required.value = e.target.checked;

      required.onChange(e, {
        type: 'ANNEX',
        field: {
          label: label.value,
          required: e.target.checked
        }
      });
      setCurrentItemInfo({
        type: 'ANNEX',
        field
      });
    }


    return (
      <div>
        <h3 className="cf-customizer-type">
          附件
        </h3>
        <div>
          <div className="cf-customizer-field cf-customizer-label">
            <div className="description">
              <span className="name">标题</span><span className="limit">最多12个字</span>
            </div>
            <div className="custom">
              <Input maxLength={12} value={label.value} onChange={onLabelChange} />
            </div>
          </div>
          <div className="cf-customizer-field cf-customizer-required">
            <div className="custom">
              <Checkbox checked={required.value} onChange={onrequiredChange}>
                设为必填
              </Checkbox>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

const BaseItems = {
  INPUT,
  TEXTAREA,
  RADIO,
  CHECKBOX,
  DATEPICKER,
  DATERANGE,
  NUMBER,
  MONEY,
  SELECT,
  ANNEX
}

export default BaseItems;

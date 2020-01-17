import React, { useEffect, useState, useRef } from 'react';
import { Layout, Button, Table, Divider, Icon, Upload, Modal, Avatar, Input, Radio, Breadcrumb, message } from 'antd';
import { History } from 'history';
import { match } from 'react-router';
import { Dispatch } from 'redux';
import { connect } from 'dva';
import { Authorization } from '../../utils/getAuthorization';
import handleTime from '../../utils/handleTime';
import handleSize from '../../utils/handleSize';
import request from '../../utils/request';
import getFileTypeBySuffix from '../../utils/getFileTypeBySuffix';
import IconFile from './iconfile';
import NoactionTable from './table-noaction';
import { RadioChangeEvent } from 'antd/lib/radio';
import { UploadChangeParam } from 'antd/lib/upload';

const { Content } = Layout;
const { confirm } = Modal;

const NAMESPACE = 'Doc';
const WORKBENCH = 'Workbench';
interface StateToCompanyDocProps {
  companys: any;
}
interface DispatchToCompanyDocProps {
  /** 获取企业文件夹信息 */
  queryCompanyFolders: (params: any, cb?: (personalFolders: any) => void) => void;
  /** 获取个人文件夹信息 */
  queryPersonalFolders: (params?: any, cb?: (personalFolders: any) => void) => void;
  /** 获取加入的公司列表信息 */
  queryCompanys: (cb?: (companys: any) => void) => void;
}

export interface CompanyDocProps extends StateToCompanyDocProps, DispatchToCompanyDocProps {
  history: History;
  /** 路由参数 */
  match: match<{ companyId: string }>
}

const mapStateToProps: (state: any) => StateToCompanyDocProps = (state) => ({
  companys: state[WORKBENCH].companys
});

const mapDispatchToProps: (dispatch: Dispatch) => DispatchToCompanyDocProps = (dispatch) => ({
  queryCompanyFolders(params, cb) {
    dispatch({
      type: `${NAMESPACE}/queryCompanyFolders`,
      payload: { params, cb }
    })
  },
  queryPersonalFolders(params, cb) {
    dispatch({
      type: `${NAMESPACE}/queryPersonalFolders`,
      payload: { params, cb }
    })
  },
  queryCompanys(cb) {
    dispatch({
      type: `${WORKBENCH}/queryCompanys`,
      payload: { cb }
    })
  }
});


function CompanyDoc(props: CompanyDocProps) {

  const {
    queryCompanyFolders,
    queryPersonalFolders,
    queryCompanys,
    match
  } = props;

  const renameRef = useRef<Input>(null);

  const [selectedRowKeys, setSelectedRowKeys] = useState([] as any[]);
  const [files, setFiles] = useState([] as any[]);
  const [path, setPath] = useState([] as any[]);
  const [editingIndex, setEditingIndex] = useState(-1);
  const [canMoveVisible, setCanMoveVisible] = useState(false);
  // 可以复制 或 移动的文件夹列表
  const [canMoveDirs, setCanMoveDirs] = useState([] as any[]);
  const [canMovePath, setCanMovePath] = useState([] as any[]);
  const [currentMoveFile, setCurrentMoveFile] = useState();
  const [moveType, setMoveType] = useState('move');
  const [uploadLoading, setUploadLoading] = useState(false);
  const [uploadType, setUploadType] = useState('');
  const [currentMoveType, setCurrentMoveType] = useState('personal');
  const [currentCompany, setCurrentCompany] = useState(match.params.companyId);
  const [batch, setBatch] = useState(false);

  useEffect(() => {
    queryCompanyFolders({
      company_id: match.params.companyId
    }, (companyFolders) => {
      const files = [...companyFolders.directories, ...companyFolders.files];
      setFiles(files);
    });
    queryPersonalFolders(null, handleCanMoveDirs)
  }, [match.params.companyId])

  /** 可以复制或移动的文件夹列表赋值 */
  function handleCanMoveDirs(personalFolders: any) {
    setCanMoveDirs([...personalFolders.directories, ...personalFolders.files])
  }
  /** 文件/夹 列表赋值 */
  function handleFiles(personalFolders: any) {
    setFiles([...personalFolders.directories, ...personalFolders.files]);
  }

  function onSelectChange(selectedRowKeys: any[]) {
    setSelectedRowKeys(selectedRowKeys);
  }

  const rowSelection = {
    selectedRowKeys,
    onChange: onSelectChange,
  };
  /** 将要复制或移动到的 目标文件夹 */
  function moveTargetDir() {
    const _canMovePath = (currentMoveType === 'company' && moveType === 'copy') ? canMovePath.filter((_, k) => k !== 0) : canMovePath;
    return _canMovePath.length === 0 ? null : _canMovePath.join('/') + '/';
  }
  /** 有选中的 */
  const hasSelected = selectedRowKeys.length > 0;

  /** 重命名文件 */
  function rename(index: number) {
    setEditingIndex(index);
  }
  /** 取消重命名文件 */
  function cancelRename() {
    setEditingIndex(-1);
  }
  /** 重命名文件 */
  async function submitRenameFile(file_id: string, suffix?: string) {
    if (renameRef.current) {
      const result = await request('/api/c_oss_update_file_name', {
        method: 'POST',
        body: {
          company_id: match.params.companyId,
          file_id,
          directory: path.join('/'),
          name: `${renameRef.current.input.value}.${suffix}`
        }
      })
      if (result.status === 'success') {
        updateFiles();
        setEditingIndex(-1);
      }
    }
  }
  /** 重命名文件夹 */
  async function submitRenameFolder(folderName: string) {
    if (renameRef.current) {
      const dirs = [path.length === 0 ? folderName + '/' : path.join('/') + '/' + folderName + '/'];
      const target_directory = path.length === 0 ? renameRef.current.input.value + '/' : path.join('/') + '/' + renameRef.current.input.value + '/';
      const result = await request('/api/c_oss_move_folder', {
        method: 'POST',
        body: {
          from_company_id: match.params.companyId,
          company_id: match.params.companyId,
          dirs,
          target_directory,
          type: 'company'
        }
      })
      if (result.status === 'success') {
        updateFiles();
        setEditingIndex(-1);
      }
    }
  }
  /** 新建文件夹 */
  function newFloder() {
    setFiles([{
      type: 'folder',
      name: 'unknown',
      actions: true
    }, ...files])
  }
  /** 提交新建文件夹 */
  async function submitNewFolder() {
    if (renameRef.current) {
      const result = await request('/api/c_oss_makeDir', {
        method: 'POST',
        body: {
          company_id: match.params.companyId,
          path: path.length === 0 ? '/' + renameRef.current.input.value + '/' : '/' + path.join('/') + '/' + renameRef.current.input.value + '/'
        }
      })
      if (result.status === 'success') {
        updateFiles();
      }
    }
  }
  /** 取消新建文件夹 */
  function cancelNewFolder() {
    setFiles(files.filter((_, k) => k !== 0))
  }
  /** 删除文件夹 */
  function deleteFolder(folderName: string) {
    confirm({
      title: '你确定要永久删除吗?',
      okText: '确定',
      okType: 'danger',
      cancelText: '取消',
      async onOk() {
        const result = await request('/api/c_oss_deleteDir', {
          method: 'POST',
          body: {
            company_id: match.params.companyId,
            path: path.join('/') + '/' + folderName
          }
        })
        if (result.status === 'success') {
          updateFiles();
        }
      }
    })
  }
  /** 删除文件 */
  function deleteFile(file_id: string) {
    confirm({
      title: '你确定要永久删除吗?',
      okText: '确定',
      okType: 'danger',
      cancelText: '取消',
      async onOk() {
        const result = await request('/api/c_oss_delete_file', {
          method: 'POST',
          body: {
            company_id: match.params.companyId,
            file_id
          }
        })
        if (result.status === 'success') {
          updateFiles();
        }
      }
    })
  }
  /** 批量删除 */
  function batchDelete() {
    confirm({
      title: '你确定要永久删除吗?',
      okText: '确定',
      okType: 'danger',
      cancelText: '取消',
      async onOk() {
        const result = await request('/api/c_oss_batchDelete', {
          method: 'POST',
          body: {
            company_id: match.params.companyId,
            dirs: batchData()[0],
            fileIds: batchData()[1]
          }
        })
        if (result.status === 'success') {
          updateFiles();
        }
      }
    })
  }
  /** 更新文件列表 */
  function updateFiles() {
    path.length !== 0
      ? queryCompanyFolders({
        company_id: match.params.companyId,
        target_directory: path.join('/') + '/'
      }, handleFiles)
      : queryCompanyFolders({
        company_id: match.params.companyId
      }, handleFiles)
  }
  /** 获取下级目录文件信息 */
  function getLowerDirectory(folderName: string, type?: string) {
    if (type === 'canMove') {
      const _canMovePath = currentMoveType === 'company' ? canMovePath.filter((_, k) => k !== 0) : canMovePath;
      const target_directory = _canMovePath.length === 0 ? folderName + '/' : _canMovePath.join('/') + '/' + folderName + '/';

      setCanMovePath([...canMovePath, folderName]);
      if (currentMoveType === 'personal') {
        queryPersonalFolders({
          target_directory
        }, handleCanMoveDirs)
      } else {
        queryCompanyFolders({
          company_id: currentCompany,
          target_directory
        }, handleCanMoveDirs)
      }
    } else {
      setPath([...path, folderName]);
      setSelectedRowKeys([]);
      queryCompanyFolders({
        company_id: match.params.companyId,
        target_directory: path.length === 0 ? folderName + '/' : path.join('/') + '/' + folderName + '/'
      }, handleFiles)
    }
  }
  /** 获取企业目录文件信息 */
  function getCompanyDirectory(folderName: string, company_id: string) {
    setCanMovePath([...canMovePath, folderName]);
    setCurrentCompany(company_id);
    queryCompanyFolders({
      company_id
    }, handleCanMoveDirs)
  }
  /** 展示可以复制的地方 */
  function showCanMoveModal(type: string, fileType: string, fileInfo: string) {
    setCanMoveVisible(true);
    setBatch(false);
    setCurrentMoveFile({
      fileType,
      fileInfo
    });
    setMoveType(type);
    setCanMovePath([]);
    if (type === 'move') {
      setCurrentMoveType('company');
      setCurrentCompany(match.params.companyId);
      queryCompanyFolders({
        company_id: match.params.companyId
      }, handleCanMoveDirs)
    } else {
      if (currentMoveType === 'company') {
        setCurrentMoveType('personal');
        queryPersonalFolders(null, (personalFolders) => {
          if (currentMoveType === 'personal' as any) {
            handleCanMoveDirs(personalFolders)
          }
        })
      }
    }
  }
  /** 展示可以批量复制 Modal */
  function showCanBatchMoveModal(type: string) {
    setCanMoveVisible(true);
    setBatch(true);
    setMoveType(type);
    setCanMovePath([]);
    if (type === 'move') {
      setCurrentMoveType('company');
      setCurrentCompany(match.params.companyId);
      queryCompanyFolders({
        company_id: match.params.companyId
      }, handleCanMoveDirs)
    } else {
      if (currentMoveType === 'company') {
        setCurrentMoveType('personal');
        queryPersonalFolders(null, (personalFolders) => {
          if (currentMoveType === 'personal' as any) {
            handleCanMoveDirs(personalFolders)
          }
        })
      }
    }
  }
  /** 个人文件和企业文件切换 */
  function handleCanMoveChange(e: RadioChangeEvent) {
    const type = e.target.value;
    setCurrentMoveType(type);
    setCanMovePath([]);
    if (type === 'personal') {
      queryPersonalFolders(null, handleCanMoveDirs)
    } else {
      queryCompanys((companys) => {
        setCanMoveDirs(companys.relate_companys.map((item: any) => ({ type: 'company', ...item })))
      })
    }
  }
  /** 提交文件复制或移动 */
  async function handleMove() {

    const { fileType, fileInfo } = currentMoveFile;
    const dirs = [path.length === 0 ? fileInfo + '/' : path.join('/') + '/' + fileInfo + '/'];

    const target_directory = currentMoveType === 'company' && moveType === 'copy'
      ? canMovePath.filter((_, k) => k !== 0).join('/') + '/'
      : canMovePath.join('/') + '/';

    let reqUrl;
    let body;

    if (moveType === 'copy') {
      if (fileType === 'folder') {
        reqUrl = '/api/c_oss_copy_folder';
        body = {
          from_company_id: match.params.companyId,
          company_id: currentCompany,
          type: currentMoveType,
          dirs,
          target_directory: target_directory === '/' ? null : target_directory
        }
      } else {
        reqUrl = '/api/c_oss_copy_file_to_path';
        body = {
          company_id: match.params.companyId,
          type: currentMoveType,
          file_id: fileInfo,
          target_directory: target_directory === '/' ? null : target_directory
        }
      }
    } else {
      if (fileType === 'folder') {
        reqUrl = '/api/c_oss_move_folder';
        body = {
          from_company_id: match.params.companyId,
          company_id: match.params.companyId,
          dirs,
          target_directory: target_directory === '/' ? null : target_directory,
          type: currentMoveType
        }
      } else {
        reqUrl = '/api/c_oss_move_file';
        body = {
          company_id: match.params.companyId,
          file_id: fileInfo,
          target_directory,
        }
      }
    }

    const successText = moveType === 'copy' ? '复制成功' : '移动成功';
    const result = await request(reqUrl, {
      method: 'POST',
      body
    })
    if (result.status === 'success') {
      message.success(successText);
      setCanMoveVisible(false);
      updateFiles();
    }
  }
  /** 返回上级 */
  function onGoback() {
    const nextPath = [...path];
    nextPath.pop();
    setPath(nextPath);
    nextPath.length !== 0
      ? queryCompanyFolders({
        company_id: match.params.companyId,
        target_directory: nextPath.join('/')
      }, handleFiles)
      : queryCompanyFolders({ company_id: match.params.companyId }, handleFiles);
  }
  /** 复制或移动文件夹 返回上级 */
  function onCanMoveGoback() {
    const nextPath = [...canMovePath];
    nextPath.pop();
    setCanMovePath(nextPath);
    nextPath.length !== 0
      ? queryCompanyFolders({
        company_id: match.params.companyId,
        target_directory: nextPath.join('/')
      }, handleCanMoveDirs)
      : queryCompanyFolders({ company_id: match.params.companyId }, handleCanMoveDirs);
  }
  /** 跳转到指定文件夹 */
  function onLink(k?: number) {
    if (k !== undefined) {
      const nextPath = path.filter((_, index) => index <= k);
      setPath(nextPath);
      queryCompanyFolders({
        company_id: match.params.companyId,
        target_directory: nextPath.join('/') + '/'
      }, handleFiles)
    } else {
      setPath([]);
      queryCompanyFolders({ company_id: match.params.companyId }, handleFiles);
    }
  }
  /** 复制或移动 跳转到指定文件夹 */
  function onCanMoveLink(k?: number) {
    if (k !== undefined) {
      const nextPath = canMovePath.filter((_, index) => index <= k);
      setCanMovePath(nextPath);
      queryCompanyFolders({
        company_id: match.params.companyId,
        target_directory: nextPath.join('/') + '/'
      }, handleCanMoveDirs)
    } else {
      setCanMovePath([]);
      queryCompanyFolders({ company_id: match.params.companyId }, handleCanMoveDirs);
    }
  }
  /** 上传中 上传完成 */
  function handleUploadChange({ file, fileList }: UploadChangeParam, type: string) {
    setUploadLoading(true);
    setUploadType(type);
    if (file.status === 'done') {
      message.success('上传成功');
      updateFiles();
      setUploadLoading(false);
    }
  }
  /** 下载文件 */
  async function downloadFile(file_id: string) {
    const result = await request('/api/oss_single_file_upload', {
      method: 'POST',
      getFile: true,
      body: {
        type: 'company',
        company_id: match.params.companyId,
        fileIds: [file_id]
      }
    })
    let blobUrl = window.URL.createObjectURL(result.blob);
    const a = document.createElement('a');
    a.download = decodeURI(result.headers.get('filename'));//获取文件名
    a.href = blobUrl;
    a.click();
    window.URL.revokeObjectURL(blobUrl);
    message.info('下载成功');
  }
  /** 单个文件夹下载 */
  function downloadFolder(filename: string) {
    downloadPackage([path.length === 0 ? filename + '/' : path.join('/') + '/' + filename + '/']);
  }
  /** 处理批量操作需要的数据 */
  function batchData() {
    const fileIds = selectedRowKeys.filter((key) => key.split('-')[0] === 'file').map((key) => key.split('-')[1]);
    const dirs = selectedRowKeys.filter((key) => key.split('-')[0] === 'folder').map((key) => path.length === 0 ? key.split('-')[1] + '/' : path.join('/') + '/' + key.split('-')[1] + '/');
    return [dirs, fileIds]
  }
  /** 批量下载 */
  function batchDownload() {
    downloadPackage(...batchData());
  }
  /** 批量处理 */
  function handleBatchMove() {
    moveType === 'copy' ? batchCopy() : batchMove()
  }
  /** 批量复制 */
  async function batchCopy() {
    const target_directory = moveTargetDir();
    const result = await request('/api/c_oss_batchCopy', {
      method: 'POST',
      body: {
        type: currentMoveType,
        target_directory,
        from_company_id: match.params.companyId,
        company_id: currentCompany,
        dirs: batchData()[0],
        fileIds: batchData()[1]
      }
    })
    if (result.status === 'success') {
      setCanMoveVisible(false);
      updateFiles();
    }
  }

  /** 批量移动 */
  async function batchMove() {
    const target_directory = moveTargetDir();
    const result = await request('/api/c_oss_batchMove', {
      method: 'POST',
      body: {
        type: currentMoveType,
        target_directory,
        from_company_id: match.params.companyId,
        company_id: match.params.companyId,
        dirs: batchData()[0],
        fileIds: batchData()[1]
      }
    })
    if (result.status === 'success') {
      setCanMoveVisible(false);
      updateFiles();
    }
  }
  /** 下载压缩包 */
  async function downloadPackage(dirs: any[] | null = null, fileIds: any[] | null = null) {
    const result = await request('/api/oss_download_package', {
      method: 'POST',
      getFile: true,
      body: {
        type: 'company',
        company_id: match.params.companyId,
        fileIds,
        dirs
      }
    })
    if (result.status === 'fail') {
      message.error(result.message);
      return
    }
    let blobUrl = window.URL.createObjectURL(result.blob);
    const a = document.createElement('a');
    a.download = decodeURI(result.headers.get('filename'));//获取文件名
    a.href = blobUrl;
    a.click();
    window.URL.revokeObjectURL(blobUrl);
    message.info('下载成功');
  }
  const columns = [{
    title: '名称',
    dataIndex: 'name',
    render: (text: string, record: any, index: number) => {

      const fileType = getFileTypeBySuffix(text);

      const icon = record.type === 'folder'
        ? <IconFile type="folder" />
        : fileType === 'img'
          ? <Avatar src={record.oss_path} />
          : <IconFile type={fileType} />

      return text === 'unknown'
        ? (
          <div className="filename" >
            {icon}
            <Input placeholder="新建文件夹" autoFocus ref={renameRef} />
            <Icon onClick={submitNewFolder} type="check-circle" theme="filled" style={{ marginLeft: 12, fontSize: 22 }} />
            <Icon onClick={cancelNewFolder} type="close-circle" theme="filled" style={{ marginLeft: 12, fontSize: 22 }} />
          </div>
        )
        : (
          <div
            className="filename"
            onClick={() => (record.type === 'folder' && editingIndex !== index)
              ? getLowerDirectory(text) : null}
          >
            {icon}
            {
              editingIndex === index
                ? (
                  <>
                    <Input defaultValue={record.type === 'file' ? text.split('.').filter((_, k, arr) => k !== arr.length - 1).join('.') : text} placeholder="新建文件夹" autoFocus ref={renameRef} />
                    <Icon onClick={() => record.type === 'file' ? submitRenameFile(record.id, text.split('.').pop()) : submitRenameFolder(text)} type="check-circle" theme="filled" style={{ marginLeft: 12, fontSize: 22 }} />
                    <Icon onClick={cancelRename} type="close-circle" theme="filled" style={{ marginLeft: 12, fontSize: 22 }} />
                  </>
                ) : <span className="filename-text">{text}</span>
            }
          </div>
        )
    },
  }, {
    title: '操作',
    width: 300,
    dataIndex: 'actions',
    align: 'center' as const,
    render: (text: string, record: any, index: number) => !text && (
      <span className="actions" style={{ marginLeft: '24px' }}>
        <span
          className="primary-color"
          onClick={() => record.type === 'folder' ? downloadFolder(record.name) : downloadFile(record.id)}
        >
          下载
        </span>
        <Divider type="vertical" />
        <span
          className="primary-color"
          onClick={() => record.type === 'folder' ? deleteFolder(record.name) : deleteFile(record.id)}
        >
          删除
        </span>
        <Divider type="vertical" />
        <span
          className="primary-color"
          onClick={() => showCanMoveModal('move', record.type, record.type === 'folder' ? record.name : record.id)}
        >
          移动
        </span>
        <Divider type="vertical" />
        <span
          className="primary-color"
          onClick={() => showCanMoveModal('copy', record.type, record.type === 'folder' ? record.name : record.id)}
        >
          复制</span>
        <Divider type="vertical" />
        <span
          className="primary-color"
          onClick={() => rename(index)}
        >
          重命名
        </span>
      </span>
    )
  }, {
    title: '上传者',
    dataIndex: 'user_name',
    width: 100,
    align: 'center' as const,
  }, {
    title: '更新时间',
    dataIndex: 'lastModified',
    width: 150,
    align: 'center' as const,
    render: (text: string) => text && handleTime(text)
  }, {
    title: '大小',
    dataIndex: 'size',
    width: 100,
    align: 'center' as const,
    render: (text: number) => text && handleSize(text)
  }];


  return (
    <Content style={{ padding: '24px', height: 'calc(100vh - 61px)' }}>
      <div style={{ padding: '24px', background: '#fff' }}>
        <div style={{ paddingBottom: '12px' }}>
          <Button
            style={{ marginRight: '12px' }}
            onClick={newFloder}
          >新建文件夹</Button>
          <Upload
            action="/api/c_oss_uploadPublicFile"
            data={{
              company_id: match.params.companyId,
              path: path.join('/') + '/'
            }}
            headers={{
              authorization: Authorization,
            }}
            showUploadList={false}
            onChange={(info) => handleUploadChange(info, 'file')}
            disabled={uploadType === 'file' && uploadLoading}
          >
            <Button loading={uploadType === 'file' && uploadLoading} style={{ marginRight: '12px' }}>
              {uploadType === 'file' && uploadLoading ? '上传中' : '上传文件'}
            </Button>
          </Upload>
          <Upload
            action="/api/c_oss_uploadPublicFile"
            data={{
              company_id: match.params.companyId,
              path: path.join('/') + '/'
            }}
            headers={{
              authorization: Authorization
            }}
            directory
            showUploadList={false}
            onChange={(info) => handleUploadChange(info, 'folder')}
            disabled={uploadType === 'folder' && uploadLoading}
          >
            <Button loading={uploadType === 'folder' && uploadLoading}>
              {uploadType === 'folder' && uploadLoading ? '上传中' : '上传文件夹'}
            </Button>
          </Upload>
        </div>

        <div>
          <Breadcrumb>
            {
              path.length === 0
                ? (
                  <Breadcrumb.Item >我的文件</Breadcrumb.Item>
                )
                : (
                  <>
                    <Breadcrumb.Item onClick={onGoback} separator="" >
                      <Icon title="返回上级" className="primary-color" style={{ cursor: 'pointer' }} type="arrow-left" />
                    </Breadcrumb.Item>
                    <Breadcrumb.Item>
                      <span className="breadcrumb-item" onClick={() => onLink()}>
                        我的文件
                      </span>
                    </Breadcrumb.Item>
                    {path.map((item: string, k, arr) => {
                      if (k < arr.length - 1) {
                        return (
                          <Breadcrumb.Item key={k}>
                            <span className="breadcrumb-item" onClick={() => onLink(k)}>
                              {item}
                            </span>
                          </Breadcrumb.Item>
                        )
                      } else {
                        return (
                          <Breadcrumb.Item key={k}>
                            <span>{item}</span>
                          </Breadcrumb.Item>
                        )
                      }
                    })}
                  </>
                )
            }
          </Breadcrumb>
          <div className="batch-actions">
            {
              hasSelected
                ? (
                  <div>
                    <span style={{ marginLeft: 8 }}>
                      {hasSelected ? `已选中 ${selectedRowKeys.length} 个文件/夹` : ''}
                    </span>
                    <span className="actions" style={{ marginLeft: '24px' }}>
                      <span className="primary-color" onClick={batchDownload}><Icon type="download" /> 下载</span>
                      <span
                        className="primary-color"
                        onClick={batchDelete}
                      >
                        删除
                      </span>
                      <span
                        className="primary-color"
                        onClick={() => showCanBatchMoveModal('move')}
                      >
                        <Icon type="right-square" />
                        移动
                      </span>
                      <span
                        className="primary-color"
                        onClick={() => showCanBatchMoveModal('copy')}
                      ><Icon type="copy" /> 复制</span>
                    </span>
                  </div>
                )
                : null
            }
          </div>
          <Table
            rowSelection={rowSelection}
            columns={columns}
            dataSource={files}
            rowKey={(record) => record.type === 'file' ? `file-${record.id}` : `folder-${record.name}`}
          />
        </div>

      </div>
      <Modal
        visible={canMoveVisible}
        title={<span style={{ marginLeft: '-8px' }}>复制到</span>}
        onCancel={() => setCanMoveVisible(false)}
        bodyStyle={{ padding: 0 }}
        onOk={batch ? handleBatchMove : handleMove}
        okText={`${moveType === 'copy' ? '复制到' : '移动到'}${canMovePath.length === 0 ? '我的文件' : canMovePath[canMovePath.length - 1]}`}
        getContainer={() => document.getElementsByClassName('doc')[0] as any}
      >
        <Radio.Group
          value={currentMoveType}
          buttonStyle="solid"
          style={{ padding: '12px 16px' }}
          onChange={handleCanMoveChange}
        >
          <Radio.Button value="personal" disabled={moveType === 'move'}>我的文件</Radio.Button>
          <Radio.Button value="company">企业文件</Radio.Button>
        </Radio.Group>
        <Breadcrumb style={{ padding: '0 16px 8px', borderBottom: '1px solid #eee' }}>
          {
            canMovePath.length === 0
              ? (
                <Breadcrumb.Item >我的文件</Breadcrumb.Item>
              )
              : (
                <>
                  <Breadcrumb.Item onClick={onCanMoveGoback} separator="" >
                    <Icon title="返回上级" className="primary-color" style={{ cursor: 'pointer' }} type="arrow-left" />
                  </Breadcrumb.Item>
                  <Breadcrumb.Item>
                    <span className="breadcrumb-item" onClick={() => onCanMoveLink()}>
                      我的文件
                    </span>
                  </Breadcrumb.Item>
                  {canMovePath.map((item: string, k, arr) => {
                    if (k < arr.length - 1) {
                      return (
                        <Breadcrumb.Item key={k}>
                          <span className="breadcrumb-item" onClick={() => onCanMoveLink(k)}>
                            {item}
                          </span>
                        </Breadcrumb.Item>
                      )
                    } else {
                      return (
                        <Breadcrumb.Item key={k}>
                          <span>{item}</span>
                        </Breadcrumb.Item>
                      )
                    }
                  })}
                </>
              )
          }
        </Breadcrumb>
        <NoactionTable
          dataSource={canMoveDirs}
          onDirectoryChange={(folderName: string) => getLowerDirectory(folderName, 'canMove')}
          onCompanyChange={getCompanyDirectory}
        />
      </Modal>

    </Content>
  )
}

export default connect(mapStateToProps, mapDispatchToProps)(CompanyDoc)

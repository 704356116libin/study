/** 获取文件类型 */
export default function getFileTypeBySuffix(fileName: string) {
  if (!fileName) return;
  const imgType = ['jpg', 'png', 'gif'];
  const suffix = fileName.split('.').pop() || '';
  return imgType.includes(suffix) ? 'img' : suffix;
}
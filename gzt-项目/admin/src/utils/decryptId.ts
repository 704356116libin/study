/**
 * 解析服务端传过来的加密id
 * @param id 
 */
export default function decryptId(id: string) {
  return id.slice(6);
}

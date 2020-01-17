const units = ['B', 'KB', 'MB', 'GB', 'TB'];
/**
 * 字节转成对应的 单位 支持 B, KB, MB, GB, TB 要支持更高的往数组后面追加对应的单位就行
 * 比如要支持 PB, 修改 units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'] 即可
 * @param size 文件大小
 * @param level 不用传, 表示当前处于哪个级别的, 跟单位数组对应 
 */
export default function handleSize(size: number, level: number = 1): string {
  return size / (1024 ** level) > 1
    ? handleSize(size, level + 1)
    : `${Math.round((size / (1024 ** (level - 1))) * 100) / 100}${units[level - 1]}`
}

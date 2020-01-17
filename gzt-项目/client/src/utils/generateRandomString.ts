
/**
 * 生成 dight 位随机字符串
 * @param dight 
 */
export default function generateRandomString(dight: number) {
    let str = '';
    for (let i = 0; i < dight; i++) {
        str += ['a', 'b', 'c', 'd'][Math.floor(Math.random() * 4)]
    }
    return str
}
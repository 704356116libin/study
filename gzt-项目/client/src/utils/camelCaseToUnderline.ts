
/** 驼峰转下划线 */
export function camelCaseToUnderline(camelCase: string) {
  return camelCase.replace(/([A-Z])+/g, substring => `_${substring.toLowerCase()}`);
}
/** 下划线转驼峰 */
export function underlineToCamelCase(underline: string) {
  // 方法一
  return underline.replace(/_+([a-zA-Z])/g, (substring, $1) => $1.toUpperCase());
  // 方法二
  // return underline.split('_').map((word, index) => index === 0 ? word : word.substring(0, 1).toUpperCase() + word.substring(1)).join('');
}
const isObject = (thing) => thing !== null && typeof thing === "object";
const { isArray } = Array;
const isUndefined = (thing) => typeof thing === "undefined";

const forEach = (obj, fn, { allOwnKeys = false } = {}) => {
  // Don't bother if no value provided
  if (!obj || isUndefined(obj)) {
    return;
  }

  // Force an array if not already something iterable
  if (!isObject(obj)) {
    obj = [obj];
  }

  //if-else can be simplified & obj.keys can be used to simplify logic, since function call is the same in both keys (minus the difference of the key)
  const keys = isArray(obj)
    ? obj.keys()
    : allOwnKeys
    ? Object.getOwnPropertyNames(obj)
    : Object.keys(obj);

  for (key of keys) {
    fn.call(null, obj[key], key, obj);
  }
};

const toJSONObject = (obj) => {
  const stack = new Array(10);

  const visit = (source, i) => {
    if (isObject(source)) {
      if (stack.includes(source)) {
        return;
      }

      if (!("toJSON" in source)) {
        stack[i] = source;
        const target = isArray(source) ? [] : {};

        forEach(source, (value, key) => {
          const reducedValue = visit(value, i + 1);
          !isUndefined(reducedValue) && (target[key] = reducedValue);
        });

        stack[i] = undefined;
        return target;
      }
    }
    return source;
  };
  return visit(obj, 0);
};

module.exports = toJSONObject;

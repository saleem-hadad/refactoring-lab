const isObject = (thing) => thing !== null && typeof thing === 'object';
const typeOfTest = type => thing => typeof thing === type;
const {isArray} = Array;
const isUndefined = typeOfTest('undefined');

function forEach(obj, fn, {allOwnKeys = false} = {}) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  let i;
  let l;

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    const keys = allOwnKeys ? Object.getOwnPropertyNames(obj) : Object.keys(obj);
    const len = keys.length;
    let key;

    for (i = 0; i < len; i++) {
      key = keys[i];
      fn.call(null, obj[key], key, obj);
    }
  }
}


const toJSONObject = (obj) => {
  const stack = new Array(10)

  const visit = (source, i) => {
    if (isObject(source)) {
      if (stack.indexOf(source) >= 0) {
        return
      }

      if (!('toJSON' in source)) {
        stack[i] = source
        const target = isArray(source) ? [] : {}

        forEach(source, (value, key) => {
          const reducedValue = visit(value, i + 1)
          !isUndefined(reducedValue) && (target[key] = reducedValue)
        })

        stack[i] = undefined

        return target
      }
    }

    return source
  }

  return visit(obj, 0)
}


module.exports = toJSONObject;
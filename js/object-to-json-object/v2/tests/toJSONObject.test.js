const assert = require('assert')
const toJSONObject = require('../toJSONObject')

test('should convert to a plain object without circular references', () => {
  const obj = { a: [0] }
  const source = { x: 1, y: 2, obj }
  source.circular1 = source
  obj.a[1] = obj

  assert.deepStrictEqual(toJSONObject(source), {
    x: 1,
    y: 2,
    obj: { a: [0] },
  })
})

test('should use objects with defined toJSON method without rebuilding', () => {
  const objProp = {}
  const obj = {
    objProp,
    toJSON() {
      return { ok: 1 }
    },
  }
  const source = { x: 1, y: 2, obj }

  const jsonObject = toJSONObject(source)

  assert.strictEqual(jsonObject.obj.objProp, objProp)
  assert.strictEqual(
    JSON.stringify(jsonObject),
    JSON.stringify({ x: 1, y: 2, obj: { ok: 1 } }),
  )
})

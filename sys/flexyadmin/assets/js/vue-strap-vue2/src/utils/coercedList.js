const coercion = {
  // Convert a string to booleam. Otherwise, return the value without modification, so if is not boolean, Vue throw a warning.
  boolean: val => (typeof val === 'string' ? (val === 'false' || val === 'null' || val === 'undefined' ? false : val === 'true' ? true : val) : val),
  // Attempt to convert a string value to a Number. Otherwise, return 0.
  number: (val, alt = null) => (typeof val === 'number' ? val : val === undefined || val === null || isNaN(Number(val)) ? alt : Number(val)),
  // Attempt to convert to string any value, except for null or undefined.
  string: val => (val === undefined || val === null ? '' : val + ''),
  // Pattern accept RegExp, function, or string (converted to RegExp). Otherwise return null.
  pattern: val => (val instanceof Function || val instanceof RegExp ? val : typeof val === 'string' ? new RegExp(val) : null)
}

function setProp(getter,setter) {
  return {
    enumerable: false,
    writable: false,
    configurable: false,
    get: getter,
    set: setter
  }
}

function ist(coerce) {
  let = {}
  Object.keys(coerce||{}).forEach(prop => {
    let p = coerce && coerce[prop]
    let fn = p instanceof Function ? p : typeof p === 'string' ? coercion[p] : null
    console.log([p,typeof p,coercion[p],fn])
    if (fn) Object.defineProperty( el, setProp(() => fn(this[prop]), value => { this[prop] = fn(value) }))
  })
  console.log(
  return coerced
}

export default ist
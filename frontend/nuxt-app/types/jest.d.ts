/* eslint-disable no-unused-vars */
import 'jest'

declare global {
  namespace jest {
    interface Expect {
      toStrictEqual: any
    }
  }
}

declare namespace jest {
  interface Matchers<R> {
    equal: (expected: any) => R
    deep: {
      equal: (expected: any) => R
    }
  }
}

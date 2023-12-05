export type Truthy<T> = T extends false | '' | 0 | null | undefined ? never : T;

const filterTruthy = <T>(value: T): value is Truthy<T> => {
  return !!value;
};

export default filterTruthy;

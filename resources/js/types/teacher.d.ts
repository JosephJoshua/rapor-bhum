export type Teacher = {
  id: number;
  name: string;
};

export type WithTeacher<T> = T & {
  teacher: Teacher;
};

export type WithOptionalTeacher<T> = T & {
  teacher?: Teacher;
};

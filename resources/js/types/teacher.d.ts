export type Teacher = {
  id: number;
  name: string;
  email: string;
  created_at: Date;
  updated_at: Date;
};

export type WithTeacher<T> = T & {
  teacher: Teacher;
};

export type WithOptionalTeacher<T> = T & {
  teacher?: Teacher;
};

export type SchoolClass = {
  id: number;
  name: string;
  created_at: Date;
  updated_at: Date;
};

export type WithModifiedSchoolClass<T, K> = T & {
  school_class: K;
};

export type WithSchoolClass<T> = T & {
  school_class: SchoolClass;
};

export type WithSchoolClasses<T> = T & {
  school_classes: SchoolClass[];
};

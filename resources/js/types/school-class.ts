import { Unit } from './unit';

export type SchoolClass = {
  id: number;
  name: string;
  created_at: Date;
  updated_at: Date;
};

export type WithSchoolClass<T> = T & {
  school_class: SchoolClass;
};

export type WithSchoolClasses<T> = T & {
  school_classes: SchoolClass[];
};

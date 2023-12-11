import { Indicator } from './indicator';

export type GradeDescriptor = {
  id: number;
  name: string;
  code: string;
  min_grade: number;
  max_grade: number;
  created_at: Date;
  updated_at: Date;
};

export type IndicatorGradeDescriptor = {
  indicator: Indicator;
  grade_descriptor: GradeDescriptor;
};

export type WithIndicatorGradeDescriptors<T> = T & {
  grade_descriptors: IndicatorGradeDescriptor[];
};

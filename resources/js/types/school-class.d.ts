import { Unit } from './unit';

export type SchoolClass = {
  id: number;
  name: string;
};

export type SchoolClassWithUnit = SchoolClass & {
  unit: Unit;
};

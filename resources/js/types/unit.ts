export type Unit = {
  id: number;
  name: string;
  created_at: Date;
  updated_at: Date;
};

export type WithUnit<T> = T & {
  unit: Unit;
};

export type WithUnits<T> = T & {
  units: Unit[];
};

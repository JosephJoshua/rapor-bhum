export type SubIndicator = {
  id: number;
  name: string;
  created_at: Date;
  updated_at: Date;
};

export type WithSubIndicators<T> = T & {
  subindicators: SubIndicator[];
};

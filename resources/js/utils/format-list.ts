const formatList = (items: string[]) => {
  if (items.length === 1) {
    return items[0];
  }

  if (items.length === 2) {
    return items.join(' dan ');
  }

  return items
    .slice(0, -1)
    .join(', ')
    .concat(` dan ${items.slice(-1)}`);
};

export default formatList;

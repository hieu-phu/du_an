export function renderStatus(status) {
  const map = {
    active: { label: "Hoạt động", color: "green" },
    inactive: { label: "Ngưng hoạt động", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function renderStatusOrderPO(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    draft: {
      label: "Nháp",
      color: "gray",
    },
    approved: {
      label: "Đã duyệt",
      color: "green",
    },
    cancelled: {
      label: "Hủy",
      color: "red",
    },
  };
  if (!map[status]) return ``;

  const item = map[status];

  return `<span class="font-medium text-${item.color}-600 whitespace-nowrap rounded-md">${item.label}</span>`;
};
export function renderStatusOrder(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    draft: {
      label: "Nháp",
      color: "gray",
    },
    approved: {
      label: "Đã duyệt",
      color: "green",
    },
    cancelled: {
      label: "Hủy",
      color: "red",
    },
  };
  if (!map[status]) return ``;

  const item = map[status];

  return `<span class="font-medium text-${item.color}-600 whitespace-nowrap rounded-md">${item.label}</span>`;
};
export function statusServiceOrderPO(status) {
  const map = {
    pending: {
      label: "Đang thực hiện",
      color: "yellow",
    },
    done: {
      label: "Đã hoàn thành",
      color: "green",
    },
  };

  if (!map[status]) return ``;

  const item = map[status];

  return `<span class="font-medium text-${item.color}-600 whitespace-nowrap rounded-md">${item.label}</span>`;
}
export function renderStatusGoodsReceivedNote(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    draft: {
      label: "Nháp",
      color: "gray",
    },
    completed: {
      label: "Đã duyệt",
      color: "green",
    },
    cancelled: {
      label: "Hủy",
      color: "red",
    },
  };

  if (!map[status]) return ``;

  const item = map[status];

  return `<span class="font-medium text-${item.color}-600 whitespace-nowrap rounded-md">${item.label}</span>`;
}
export function statusOrders(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    draft: {
      label: "Nháp",
      color: "gray",
    },
    approved: {
      label: "Đã duyệt",
      color: "green",
    },
    cancelled: {
      label: "Hủy",
      color: "red",
    },
  };

  if (!map[status]) return ``;

  const item = map[status];

  return `<span class="font-medium text-${item.color}-600 whitespace-nowrap rounded-md">${item.label}</span>`;
}

export function typeTransactionTypes(status) {
  const map = {
    income: { label: "Thu vào", color: "green" },   // Tiền vào
    outcome: { label: "Chi ra", color: "red" },     // Tiền ra
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}

export function paymentStatusOrders(status) {
  const map = {
    pending: { label: "Chưa thanh toán", color: "red" },
    partial: { label: "Một phần", color: "yellow" },
    paid: { label: "Đã thanh toán", color: "green" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}

export function warehouseOutgoingStatusOrders(status) {
  const map = {
    xuat_kho_1_phan: { label: "Xuất kho một phần", color: "yellow" },
    chua_xuat: { label: "Chưa xuất", color: "gray" },
    day_du: { label: "Đầy đủ", color: "green" },
    huy: { label: "Hủy", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function renderWarehouseStatusOrderPO(status) {
  const map = {
    nhap_kho_1_phan: { label: "Nhập kho một phần", color: "yellow" },
    chua_nhap: { label: "Chưa nhập", color: "gray" },
    day_du: { label: "Đầy đủ", color: "green" },
    tra_hang: { label: "Trả hàng", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}


export function renderSupplierType(type) {
  const map = {
    individual: "Cá nhân",
    company: "Công ty",
    agency: "Đại lý",
  };

  return `<span class="text-gray-800 dark:text-gray-200">${map[type] || type}</span>`;
}
export function renderProductsType(type) {
  const map = {
    hang_hoa: {
      label: "Hàng hóa",
      class: "text-blue-700",
    },
    vat_tu: {
      label: "Vật tư",
      class: "text-yellow-700",
    },
    dich_vu: {
      label: "Dịch vụ",
      class: "text-purple-700",
    },
  };

  const item = map[type];

  return `
    <span class="px-2 py-1 text-600 rounded-full ${
      item?.class || "bg-gray-100 text-gray-700 border border-gray-200"
    }">
      ${item?.label || type}
    </span>
  `;
}
export function renderStatusProducts(status) {
  const map = {
    active: { label: "Hoạt động", color: "green" },
    inactive: { label: "Ngưng hoạt động", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function typePartners(status) {
  const map = {
    customer: { label: "Khách hàng", color: "green" },
    supplier: { label: "Nhà cung cấp", color: "orange" },
    both: { label: "Cả hai", color: "purple" },
    employee: { label: "Nhân sự", color: "blue" },
  };

  if (!map[status]) return "";

  return `
    <span class="font-medium text-${map[status].color}-600">
      ${map[status].label}
    </span>
  `;
}

export function statusPartners(status) {
  const map = {
    active: { label: "Hoạt động", color: "green" },
    inactive: { label: "Ngưng hoạt động", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function renderStatusUnits(status) {
  const map = {
    active: { label: "Hoạt động", color: "green" },
    inactive: { label: "Ngưng hoạt động", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function typeFinancialEntries(status) {
  const map = {
    receivable: { label: "Phải thu", color: "green" },
    payable: { label: "Phải chi", color: "red" },
  };

  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function statusFinancialStatus(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    partial: {
      label: "Một phần",
      color: "gray",
    },
    completed: {
      label: "Hoàn thành",
      color: "green",
    },
    overdue: {
      label: "Quá hạn",
      color: "red",
    },
  };


  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function accountantStatusOrderPO(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    approved: {
      label: "Đã ghi nhận",
      color: "blue",
    },
    done: {
      label: "Đã hoàn thành",
      color: "green",
    },
    cancelled: {
      label: "Hủy",
      color: "red",
    },
  };


  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function accountantStatusOrders(status) {
  const map = {
    pending: {
      label: "Chờ xử lý",
      color: "yellow",
    },
    approved: {
      label: "Đã duyệt",
      color: "green",
    },
    cancelled: {
      label: "Hủy",
      color: "red",
    },
  };


  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
export function typeOrdersPO(status) {
  const map = {
    product: {
      label: "Hàng hóa",
      color: "yellow",
    },
    service: {
      label: "Dịch vụ",
      color: "green",
    },
  };


  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}

export function typeOrders(status) {
  const map = {
    product: {
      label: "Hàng hóa",
      color: "yellow",
    },
    service: {
      label: "Dịch vụ",
      color: "green",
    },
  };


  if (!map[status]) return ``;

  return `<span class="font-medium text-${map[status].color}-600">${map[status].label}</span>`;
}
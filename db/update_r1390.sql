# Cleanup double cfg: b_single_row -> int_max_rows=1

ALTER TABLE `cfg_table_info` DROP `b_single_row`;
ALTER TABLE `cfg_table_info` DROP `int_max_subrows`;


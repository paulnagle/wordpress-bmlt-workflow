import { t } from 'testcafe';
import { Selector } from 'testcafe';

export async function select_dropdown_by_id(element, id) {
    await t
    .click(element)
    .click((element).find('option').withAttribute('id',id))
};

export async function select_dropdown_by_text(element, text) {
    await t
    .click(element)
    .click((element).find('option').withText(text))
};

export async function select_dropdown_by_value(element, value) {
    await t
    .click(element)
    .click((element).find('option').withAttribute('value', value))
};

export async function select_select2_dropdown_by_value(element, value) {
    await t
    .click(element)
    .click((element).find('option').withAttribute('value', value))
};

export async function click_table_row_column(element, row, column) {
    const g = element.child('tbody').child(row).child(column);

    await t 
    .click(g);
};

export async function click_dt_button_by_index(element, index) {
    const g = element.find('button').nth(index);

    await t 
    .click(g);
};

export async function get_table_row_col(element, row, column) {
    return element.child('tbody').child(row).child(column);
};

export async function click_dialog_button_by_index(element, index) {
    const g = element.find('button').nth(index);

    await t 
    .click(g);
};
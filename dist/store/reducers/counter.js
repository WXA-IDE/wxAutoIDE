'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _handleActions;

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _reduxActions = require('./../../npm/redux-actions/lib/index.js');

var _counter = require('./../types/counter.js');

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

exports.default = (0, _reduxActions.handleActions)((_handleActions = {}, _defineProperty(_handleActions, _counter.INCREMENT, function (state) {
  return _extends({}, state, {
    num: state.num + 1
  });
}), _defineProperty(_handleActions, _counter.DECREMENT, function (state) {
  return _extends({}, state, {
    num: state.num - 1
  });
}), _defineProperty(_handleActions, _counter.ASYNC_INCREMENT, function (state, action) {
  return _extends({}, state, {
    asyncNum: state.asyncNum + action.payload
  });
}), _handleActions), {
  num: 0,
  asyncNum: 0
});
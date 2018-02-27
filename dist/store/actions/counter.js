'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.asyncInc = undefined;

var _counter = require('./../types/counter.js');

var _reduxActions = require('./../../npm/redux-actions/lib/index.js');

var asyncInc = exports.asyncInc = (0, _reduxActions.createAction)(_counter.ASYNC_INCREMENT, function () {
  return new Promise(function (resolve) {
    setTimeout(function () {
      resolve(1);
    }, 1000);
  });
});
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = undefined;

var _dec, _class;

var _wepy = require('./../npm/wepy/lib/wepy.js');

var _wepy2 = _interopRequireDefault(_wepy);

var _wepyRedux = require('./../npm/wepy-redux/lib/index.js');

var _counter = require('./../store/types/counter.js');

var _actions = require('./../store/actions/index.js');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Counter = (_dec = (0, _wepyRedux.connect)({
  stateNum: function stateNum(state) {
    return state.counter.num;
  },
  asyncNum: function asyncNum(state) {
    return state.counter.asyncNum;
  }
}, {
  incNum: _counter.INCREMENT,
  decNum: _counter.DECREMENT,
  asyncInc: _actions.asyncInc
}), _dec(_class = function (_wepy$component) {
  _inherits(Counter, _wepy$component);

  function Counter() {
    var _ref;

    var _temp, _this, _ret;

    _classCallCheck(this, Counter);

    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    return _ret = (_temp = (_this = _possibleConstructorReturn(this, (_ref = Counter.__proto__ || Object.getPrototypeOf(Counter)).call.apply(_ref, [this].concat(args))), _this), _initialiseProps.call(_this), _temp), _possibleConstructorReturn(_this, _ret);
  }

  return Counter;
}(_wepy2.default.component)) || _class);

var _initialiseProps = function _initialiseProps() {
  var _this2 = this;

  this.props = {
    num: {
      type: [Number, String],
      coerce: function coerce(v) {
        return +v;
      },
      default: 50
    }
  };
  this.data = {};
  this.events = {
    'index-broadcast': function indexBroadcast() {
      var _ref2;

      var $event = (_ref2 = arguments.length - 1, arguments.length <= _ref2 ? undefined : arguments[_ref2]);
      console.log(_this2.$name + ' receive ' + $event.name + ' from ' + $event.source.$name);
    }
  };
  this.watch = {
    num: function num(curVal, oldVal) {
      console.log('\u65E7\u503C\uFF1A' + oldVal + '\uFF0C\u65B0\u503C\uFF1A' + curVal);
    }
  };
  this.methods = {
    plus: function plus() {
      this.num = this.num + 1;
      console.log(this.$name + ' plus tap');

      this.$emit('index-emit', 1, 2, 3);
    },
    minus: function minus() {
      this.num = this.num - 1;
      console.log(this.$name + ' minus tap');
    }
  };
};

exports.default = Counter;